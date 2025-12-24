<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\VideoUpdateRequest;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\VideoResource;
use App\Jobs\CreateVideoJob;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\Tag;
use App\Models\TagVideo;
use App\Models\User;
use App\Models\Video;
use App\Services\FormatterService;
use App\Services\RecommendationService;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoController extends Controller
{
    /**
     * Get all videos
     * @param Request $request
     * @param int $start
     * @param int $count
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, int $start, int $count)
    {
        if ($request->get('category')) {
            return VideoResource::collection(Video::where([
                'category_id' => $request->get('category'), 'public' => 1
            ])->skip($start)->take($count)->get());
        }
        return VideoResource::collection(Video::where([
            'public' => 1
        ])->skip($start)->take($count)->get());
    }

    /**
     * Get video exists playlists
     * @param Request $request
     * @param Video $video
     * @return mixed
     */
    public function video_playlists(Request $request, Video $video)
    {
        $all = PlaylistResource::collection($request->user('api')->playlists);
        $used = $video->playlists()
            ->whereIn('playlist_id', $request->user()->playlists->pluck('id')->toArray())
            ->get();

        return $all->map(function ($item) use ($used) {
            $playlist = Playlist::query()->find($item->id);
            return [
                'id' => $playlist->id,
                'title' => $playlist->title,
                'public' => $playlist->public,
                'active' => $used->contains('playlist_id', $playlist->id),
            ];
        });
    }


    /**
     * Search video/playlist
     * @param SearchRequest $request
     * @param int $start
     * @param int $count
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function search(SearchRequest $request, int $start, int $count)
    {
        $videos = SearchService::video($request)->paginate($count, ['*'], 'page', $start);
        $playlists = SearchService::playlist($request)->paginate($count, ['*'], 'page', $start);
        return match ($request->type) {
            'video' => VideoResource::collection($videos)
                ->additional(['count' => $videos->total()]),
            'playlist' => PlaylistResource::collection($playlists)
                ->additional(['count' => $playlists->total()]),
            'all' => response()->json([
                'videos' => [
                    'items' => VideoResource::collection($videos),
                    'count' => $videos->total(),
                ],
                'playlists' => [
                    'items' => PlaylistResource::collection($playlists),
                    'count' => $playlists->total(),
                ],
                'count' => $videos->total() + $playlists->total(),
            ]),
            default => throw new ApiException(404, 'Unknown search type'),
        };
    }

    /**
     * Get user's videos
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user)
    {
        if (auth('api')->id() === $user->id) {
            return response()->json([
                'user' => ChannelResource::make($user),
                'videos' => VideoResource::collection(Video::where(['user_id' => $user->id])->get())]);
        }
        return response()->json([
            'user' => ChannelResource::make($user),
            'videos' => VideoResource::collection(Video::where([
                'user_id' => $user->id,
                'public' => 1
            ])->get())]);
    }

    /**
     * Watching video
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_video(Video $video)
    {
        ApiRequest::private($video, 'video');
        return response()->json([
            'video' => VideoResource::make($video),
            'recommendations' => VideoResource::collection(RecommendationService::get($video))
        ]);
    }

    /**
     * Change status on private
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function private(Video $video)
    {
        ApiRequest::status($video, 'video', 0);
        PlaylistVideo::where('video_id', '=', $video->id)->whereIn('playlist_id', Playlist::where('user_id', '!=', auth('api')->id())->pluck('id'))->delete();
        return parent::status($video, 0);
    }

    /**
     * Change status on public
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function public(Video $video)
    {
        ApiRequest::status($video, 'video', 1);
        return parent::status($video, 1);
    }

    /**
     * Meta-data for video
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function meta(Request $request)
    {
        $request->validate([
            'title' => 'required|string|unique:videos,title',
            'description' => 'required|string',
            'thumbnail' => 'image|mimes:jpeg,png,jpg|dimensions:ratio=16/9,min_height=720,min_width=1280',
            'category_id' => 'exists:categories,id'
        ]);

        $thumbnail = $request->file('thumbnail');
        $thumbnail ? $thumbnail = $thumbnail->store('previews') : $thumbnail = null;

        $token = Str::uuid()->toString();
        Cache::put($token, [
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail' => $thumbnail,
            'category_id' => $request->category_id,
            'public' => $request->public,
        ], now()->addMinutes(30));

        return response()->json(['data' => ['upload_token' => $token]]);
    }

    /**
     * Chunk upload video
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $receiver = new FileReceiver('file', $request, ResumableJSUploadHandler::class);
            $save = $receiver->receive();
            $handler = $save->handler();

            if ($save->isFinished()) {
                $file = $save->getFile();
                if (!in_array($file->getMimeType(), ['video/mp4', 'video/x-m4v', 'video/ogg', 'video/webm'])) {
                    unlink($file);
                    return response()->json(['errors' => ['video' => ['Invalid video format']]], 422);
                }
                $fileName = $file->hashName();
                $folder = $file->move(Storage::disk('local')->path('/videos'), $fileName)->getPathname();
                $default = substr($folder, strpos($folder, '../') + 3);

                $path = [
                    'default' => $default,
                    'hls' => Str::uuid()->toString() . '/index.m3u8'
                ];

                $meta = Cache::get($request->upload_token);
                if (!$meta['thumbnail']) {
                    $meta['thumbnail'] = FormatterService::preview($default);
                }

                CreateVideoJob::dispatch($path, $request->user('api')->id, $meta);
                return response()->json(['data' => [
                    'progress' => $handler->getPercentageDone(),
                    'message' => "В ближайшее время видео будет добавлено"
                ]])->setStatusCode(201);
            }

            return response()->json([
                'progress' => $handler->getPercentageDone()
            ]);
        } catch (\Throwable $e) {
            Log::error('Chunk upload failed', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5),
                'request' => [
                    'chunk' => $request->resumableChunkNumber ?? null,
                    'totalChunks' => $request->resumableTotalChunks ?? null,
                    'identifier' => $request->resumableIdentifier ?? null,
                    'totalSize' => $request->resumableTotalSize ?? null,
                ],
            ]);

            return response()->json([
                'error' => 'Upload failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Playing video
     * @param $folder
     * @param $filename
     * @return \ProtoneMedia\LaravelFFMpeg\Http\DynamicHLSPlaylist
     */
    public function getVideo($folder, $filename)
    {
        return FFMpeg::dynamicHLSPlaylist('local')
            ->open("media/$folder/$filename")
            ->setMediaUrlResolver(function ($filename) use ($folder) {
                return route("video.file", ["folder" => $folder, "filename" => $filename]);
            })
            ->setKeyUrlResolver(function ($key) use ($folder) {
                return route("video.key", ["key" => $key]);
            })
            ->setPlaylistUrlResolver(function ($filename) use ($folder) {
                return route("video.playlist", ["folder" => $folder, "filename" => $filename]);
            });
    }

    /**
     * Get key for private video
     * @param $key
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getKey($key)
    {
        abort_if(!auth('api')->check(), 403, 'Forbidden');
        return Storage::disk('media')->download("keys/$key");
    }

    /**
     * Get segments of video
     * @param $folder
     * @param $filename
     * @return string|null
     */
    public function getFile($folder, $filename)
    {
        return Storage::disk("media")->get("$folder/$filename");
    }

    /**
     * Update info of video
     * @param VideoUpdateRequest $request
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VideoUpdateRequest $request, Video $video)
    {
        $video->update($request->all());
        return parent::response($video, 'updated', 'Видео успешно обновлено');
    }

    /**
     * Add tag in video
     * @param Video $video
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_tag(Video $video, Tag $tag)
    {
        /** Not his video */
        ApiRequest::action($video, 'add tags', 'to this video');

        /** Tag exists */
        if ($video->tags->where('tag_id', $tag->id)->first()) {
            throw new ApiException(402, 'Video already exists this tag');
        }

        TagVideo::create([
            'tag_id' => $tag->id,
            'video_id' => $video->id
        ]);
        return parent::response($tag, 'added to video', 'Тег успешно добавлен');
    }

    /**
     * Delete tag from video
     * @param Video $video
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy_tag(Video $video, Tag $tag)
    {
        ApiRequest::action($video, 'delete tags', 'from this video');

        /** Tag not exists */
        if (!$video->tags->where('video_id', $video->id)->where('tag_id', $tag->id)->first()) {
            throw new ApiException(402, "Tag doesn't exist in this video");
        }

        TagVideo::where([
            'tag_id' => $tag->id,
            'video_id' => $video->id
        ])->delete();
        return parent::response($tag, 'deleted from video', 'Тег успешно удален');
    }


    /**
     * Delete video
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Video $video)
    {
        ApiRequest::action($video, 'delete', 'this video');
        $disk = Storage::disk('local');
        $disk->delete([$video->thumbnail, 'videos/' . basename(dirname($video))]);
        $disk->deleteDirectory('media/' . basename(dirname($video)));
        return parent::delete($video);
    }
}
