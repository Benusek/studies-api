<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\PrivateVideoRequest;
use App\Http\Requests\PublicVideoRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\TagAddRequest;
use App\Http\Requests\TagDeleteRequest;
use App\Http\Requests\VideoDeleteRequest;
use App\Http\Requests\VideoShowRequest;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Pion\Laravel\ChunkUpload\Handler\ResumableJSUploadHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoController extends Controller
{
    /**
     * Просмотр всех видео
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
     * Функция для сортировки видео с нужными тегами
     * @param $videos
     * @param $tags
     * @return array
     */
    public function filterTags($videos, $tags)
    {
        $search = array();
        foreach ($videos as $video) {
            if (array_intersect($video->tags->pluck('tag_id')->toArray(), $tags)) {
                array_push($search, $video);
            }
        }
        return $search;
    }

    /**
     * Просмотр своих плейлистов с конкретным видео
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
     * Функция для получения отфильтрованных видео
     * @param $request
     * @param $tags
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    //TODO: Перепроверить, сделано не лучшим образом
    public function filterGetVideos($request)
    {
        $tags = $request->get('tags');
        $users = User::where('name', 'LIKE', "%{$request->get('query')}%")->get()->pluck('id')->toArray();
        $videos = Video::where('title', 'LIKE', "%{$request->get('query')}%")
            ->orWhere(function ($request) use ($users) {
                $request->whereIn('user_id', $users);
            })->where('public', 1)->get();
        if ($request->get('categories') && count($request->get('categories')) > 0) {
            $videos = $videos->whereIn('category_id', $request->get('categories'));
        }
        if ($request->get('tags') && count($request->get('tags')) > 0) {
            return VideoResource::collection($this->filterTags($videos, $tags));
        }
        return VideoResource::collection($videos);
    }

    /**
     * Функция для получения отфильтрованных плейлистов
     * @param $request
     * @return \Illuminate\Support\Collection
     */
    //TODO: Перепроверить, сделано не лучшим образом
    public function filterGetPlaylists($request)
    {
        $tags = $request->get('tags');
        $search_middle = array();
        $playlists = Playlist::where(
            'title', 'LIKE', "%{$request->get('query')}%",
        )->where(['public' => 1])->get();

        //Сбор плейлистов в котором есть хотя бы одно видео
        foreach ($playlists as $playlist) {
            if (count($playlist->videos) !== 0) {
                array_push($search_middle, $playlist);
            }
        }

        $playlists = $search_middle;
        $search_middle = [];

        if ($request->get('categories')) {
            $video_with_category = 0;
            foreach ($playlists as $playlist) {
                foreach ($playlist->videos as $video) {
                    if (in_array($video->category_id, $request->get('categories'))) {
                        $video_with_category++;
                    }
                }
                if ($video_with_category >= $playlist->videos->count() / 2) {
                    array_push($search_middle, $playlist);
                }
            }
            $playlists = $search_middle;
            $search_middle = [];
        }
        if ($request->get('tags')) {
            foreach ($playlists as $playlist) {
                $current_videos = $this->filterTags($playlist->videos, $tags);
                if (count($current_videos) >= $playlist->videos->count()) {
                    array_push($search_middle, $playlist);
                }
            }
            $playlists = $search_middle;
        }
        return collect($playlists);
    }

    //TODO: Перепроверить, сделано не лучшим образом

    /**
     * Фильтр для поиска видео/плейлиста
     * @param SearchRequest $request
     * @param int $start
     * @param int $count
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|void
     */
    public function search(SearchRequest $request, int $start, int $count)
    {
        switch ($request->get('type')) {
            case 'video':
                return VideoResource::collection($this->filterGetVideos($request)->slice($start, $count));
            case 'playlist':
                return PlaylistResource::collection($this->filterGetPlaylists($request)->slice($start, $count));
            case 'all':
                $videos = $this->filterGetVideos($request);
                $playlists = $this->filterGetPlaylists($request);

                return response()->json([
                    'data' => [
                        'count_playlists' => count($playlists),
                        'count_videos' => count($videos),
                        'videos' => count($videos->slice($start, $count)) ? VideoResource::collection($videos->slice($start, $count)) : null,
                        'playlists' => count($playlists->slice($start, $count)) ? PlaylistResource::collection($playlists->slice($start, $count)) : null,
                    ]
                ]);
        }
        throw new ApiException(404, 'Not found videos/playlists with this parameters');
    }

    /**
     * Просмотр видео пользователя
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Request $request, User $user)
    {
        if ($request->user->id === $user->id) {
            return response()->json([
                'user' => ChannelResource::make($user),
                'videos' => VideoResource::collection(Video::where(['user_id' => $user->id])->get())]);
        }
        return response()->json([
            'user' => ChannelResource::make($user),
            'videos' => VideoResource::collection(Video::where([
                'user_id' => $user->id,
                'public' => 1
            ])->get()),]);
    }

    public function recommendation(VideoShowRequest $request, Video $video)
    {
        $videos = Video::where('public', 1)->get();
        $videos = $videos->where('category_id', '=', $video->category_id);
        if (count($video->tags) === 0) {
            return VideoResource::collection($videos);
        }
        return VideoResource::collection($this->filterTags($videos, $video->tags->pluck('tag_id')->toArray()));
    }

    /**
     * Просмотр одного видео
     * @param VideoShowRequest $request
     * @param Video $video
     * @return VideoResource
     */
    public function show_video(VideoShowRequest $request, Video $video)
    {
        return VideoResource::make($video);
    }

    /**
     * Изменение статуса видео на приватное
     * @param PrivateVideoRequest $request
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function private(PrivateVideoRequest $request, Video $video)
    {
        PlaylistVideo::where('video_id', '=', $video->id)->whereIn('playlist_id', Playlist::where('user_id', '!=', $request->user('api')->id)->pluck('id'))->delete();
        return parent::status($video, 0);
    }

    /**
     * Изменение статуса видео на публичное
     * @param PublicVideoRequest $request
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function public(PublicVideoRequest $request, Video $video)
    {
        return parent::status($video, 1);
    }

    public function store(Request $request)
    {
        $receiver = new FileReceiver('video', $request, ResumableJSUploadHandler::class);
        $save = $receiver->receive();
        $handler = $save->handler();

        if ($save->isFinished()) {
            $request->validate([
                'title' => 'required|string|unique:videos,title',
                'description' => 'required|string',
                'thumbnail' => 'image|mimes:jpeg,png,jpg|dimensions:ratio=16/9,min_height=720,min_width=1280',
                'category_id' => 'exists:categories,id'
            ]);

            $file = $save->getFile();

            if (in_array($file->getMimeType(), ['video/mp4', 'video/ogg', 'video/webm', 'video/quicktime'])) {
                unlink($file);
                return response()->json(['errors' => ['video' => ['Invalid video format']]], 422);
            }
            $fileName = $file->hashName();
            $folder = $file->move(Storage::disk('local')->path('/videos'), $fileName)->getPathname();
            $default = substr($folder, strpos($folder, '../') + 3);

            $path = [
                'default' => $default,
                'hls' => pathinfo($request->file('video'), PATHINFO_FILENAME) . '/index.m3u8'
            ];
            $thumbnail = $request->file('thumbnail');
            $thumbnail ? $thumbnail = $thumbnail->store('previews') : $thumbnail = $this->getPreview($default);
            CreateVideoJob::dispatch($thumbnail, $path, $request->user('api')->id, $request->except(['video', 'thumbnail']));

            return response()->json([
                'progress' => $handler->getPercentageDone(),
                'message' => "В ближайшее время видео будет добавлено"
            ])->setStatusCode(201);
        }

        return response()->json([
            'progress' => $handler->getPercentageDone()
        ]);
    }

    protected function getPreview($file)
    {
        $path = 'previews/' . pathinfo($file, PATHINFO_FILENAME) . '.jpeg';
        FFMpeg::fromDisk('local')
            ->open($file)
            ->getFrameFromSeconds(0)
            ->export()
            ->toDisk('local')
            ->save($path);
        return $path;
    }

    public function getVideo($folder, $filename)
    {
        return FFMpeg::dynamicHLSPlaylist('local')
            ->open("media/$folder/$filename")
            ->setMediaUrlResolver(function ($filename) use ($folder) {
                return route("video.file", ["folder" => $folder, "filename" => $filename]);
            })
            ->setPlaylistUrlResolver(function ($filename) use ($folder) {
                return route("video.playlist", ["folder" => $folder, "filename" => $filename]);
            });
    }

    public function getFile($folder, $filename)
    {
        return Storage::disk("media")->get("$folder/$filename");
    }

    /**
     * @param VideoUpdateRequest $request
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VideoUpdateRequest $request, Video $video)
    {
        $video->update($request->all());
        return parent::response($video, 'updated', 'Видео успешно обновлено')->setStatusCode(201, 'Updated');
    }

    /**
     * Добавление тега к видео
     * @param TagAddRequest $request
     * @param Video $video
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_tag(TagAddRequest $request, Video $video, Tag $tag)
    {
        TagVideo::create([
            'tag_id' => $tag->id,
            'video_id' => $video->id
        ]);
        return parent::response($tag, 'added to video', 'Тег успешно добавлен');
    }

    /**
     * Удаление тега у видео
     * @param TagDeleteRequest $request
     * @param Video $video
     * @param Tag $tag
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy_tag(TagDeleteRequest $request, Video $video, Tag $tag)
    {
        TagVideo::where([
            'tag_id' => $tag->id,
            'video_id' => $video->id
        ])->delete();
        return parent::response($tag, 'deleted from video', 'Тег успешно удален');
    }


    /**
     * Удаление видео
     * @param VideoDeleteRequest $request
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(VideoDeleteRequest $request, Video $video)
    {
        return parent::delete($video);
    }
}
