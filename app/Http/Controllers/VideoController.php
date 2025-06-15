<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\PrivateVideoRequest;
use App\Http\Requests\PublicVideoRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\TagAddRequest;
use App\Http\Requests\TagDeleteRequest;
use App\Http\Requests\VideoAddRequest;
use App\Http\Requests\VideoDeleteRequest;
use App\Http\Requests\VideoUpdateRequest;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\VideoResource;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\Tag;
use App\Models\TagVideo;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Просмотр всех видео
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, int $start, int $count)
    {
        if ($request->get('category')) {
            return VideoResource::collection(Video::where(['category_id' => $request->get('category'), 'public' => 1])->get()->slice($start, $count));
        }
        if ($request->get('query')) {
            $users = User::where('name', 'LIKE', "%{$request->get('query')}%")->get()->pluck('id')->toArray();
            return VideoResource::collection(Video::where('title', 'LIKE', "%{$request->get('query')}%")
                ->orWhere(function ($request) use ($users) {
                    $request->whereIn('user_id', $users);
                })->where('public', 1)->get()->slice($start, $count));
        }
        return VideoResource::collection(Video::where([
            'public' => 1
        ])->get()->slice($start, $count));
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
     * Функция для получения отфильтрованных видео
     * @param $request
     * @param $tags
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function filterGetVideos($request, $tags)
    {
        $users = User::where('name', 'LIKE', "%{$request->get('query')}%")->get()->pluck('id')->toArray();
        $videos = Video::where('title', 'LIKE', "%{$request->get('query')}%")
            ->orWhere(function ($request) use ($users) {
                $request->whereIn('user_id', $users);
            })->where('public', 1);
        if ($request->get('categories')) {
            $videos = $videos->whereIn('category_id', $request->get('categories'))->get();
            $videos = VideoResource::collection($videos);
        }
        if ($request->get('tags')) {
            $videos = VideoResource::collection($this->filterTags($videos, $tags));
        }
        if (!$request->get('tags') && !$request->get('categories')) {
            $videos = $videos->get();
            $videos = VideoResource::collection($videos);
        }
        return $videos;
    }

    /**
     * Функция для получения отфильтрованных плейлистов
     * @param $request
     * @param $tags
     * @return array
     */
    public function filterGetPlaylists($request, $tags)
    {
        $search = array();
        $search_middle = array();
        $playlists = Playlist::where(
            'title', 'LIKE', "%{$request->get('query')}%",
        )->where(['public' => 1])->get();
        if ($request->get('categories')) {
            $video_with_category = 0;
            foreach ($playlists as $playlist) {

                foreach ($playlist->videos as $video) {
                    if (in_array($video->category_id, $request->get('categories'))) {
                        $video_with_category++;
                    }
                }
                if ($video_with_category >= $playlist->videos->count() / 2) {
                    array_push($search, $playlist);
                }
            }
        }
        if ($request->get('tags')) {
            foreach ($search as $playlist) {
                $current_videos = $this->filterTags($playlist->videos, $tags);
                if (count($current_videos) >= $playlist->videos->count()) {
                    array_push($search_middle, $playlist);
                }
            }
            $search = $search_middle;
        }
        return collect($search);
    }

    /**
     * Фильтр для поиска видео/плейлиста
     * @param SearchRequest $request
     * @param int $start
     * @param int $count
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection|void
     */
    public function search(SearchRequest $request, int $start, int $count)
    {
        $tags = $request->get('tags');
        switch ($request->get('type')) {
            case 'video':
                return VideoResource::collection($this->filterGetVideos($request, $tags)->slice($start, $count));
            case 'playlist':
                return PlaylistResource::collection($this->filterGetPlaylists($request, $tags)->slice($start, $count));
            case 'all':
                $videos = $this->filterGetVideos($request, $tags)->slice($start, $count);
                $playlists = $this->filterGetPlaylists($request, $tags)->slice($start, $count);

                return response()->json([
                    'data' => [
                        'count_playlists' => count($playlists),
                        'count_videos' => count($videos),
                        'videos' => count($videos) ? VideoResource::collection($videos) : null,
                        'playlists' => count($playlists) ? PlaylistResource::collection($playlists) : null,
                    ]
                ]);
        }
        throw new ApiException(404, 'Not found videos/playlists with this parameters');
    }

    /**
     * Просмотр видео пользователя
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Request $request, User $user)
    {
        if ($request->user('api')->id === $user->id) {
            return VideoResource::collection(Video::where(['user_id' => $user->id])->get());
        }
        return VideoResource::collection(Video::where([
            'user_id' => $user->id,
            'public' => 1
        ])->get());
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

    /**
     * Добавление видео
     * @param VideoAddRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(VideoAddRequest $request)
    {
        $video = Video::create([
                'photo_file' => $request->photo_file ? $request->photo_file->store('video_previews') : null,
                'video_file' => $request->video_file ? $request->video_file->store('user_videos') : null,
                'user_id' => $request->user('api')->id] + $request->all()
        );
        return parent::response($video, 'created')->setStatusCode(201, 'Created');
    }

    /**
     * Изменение содержимого видео
     * @param VideoUpdateRequest $request
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(VideoUpdateRequest $request, Video $video)
    {
        $video->update($request->all());
        return parent::response($video, 'updated')->setStatusCode(201, 'Updated');
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
        return parent::response($tag, 'added to video');
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
        return parent::response($tag, 'deleted from video');
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
