<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrivateVideoRequest;
use App\Http\Requests\PublicVideoRequest;
use App\Http\Requests\TagAddRequest;
use App\Http\Requests\TagDeleteRequest;
use App\Http\Requests\VideoAddRequest;
use App\Http\Requests\VideoDeleteRequest;
use App\Http\Requests\VideoUpdateRequest;
use App\Http\Resources\VideoResource;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\Tag;
use App\Models\TagVideo;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    /**
     * Просмотр всех видео
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request, int $start,  int $count)
    {
        if ($request->user('api') === null) {
            return VideoResource::collection(Video::where([
                'public' => 1
            ])->get()->slice($start, $count));
        }
        return VideoResource::collection(Video::where([
            'public' => 1
        ])->orWhere(['user_id' => $request->user('api')->id])->get()->slice($start, $count));
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
        PlaylistVideo::where('video_id', '=', $video->id)->whereIn('playlist_id',  Playlist::where('user_id', '!=', $request->user('api')->id)->pluck('id'))->delete();
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
