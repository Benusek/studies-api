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
use App\Models\Tag;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Просмотр всех видео
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return VideoResource::collection(Video::all());
    }

    /**
     * Просмотр своих видео
     * @return string
     */
    public function show()
    {
        //        return (User::where("id", "=", "1")->first()->videos);
        return "show_videos";
    }

    /**
     * Изменение статуса видео на приватное
     * @return string
     */
    public function private(PrivateVideoRequest $request, Video $video)
    {
        return "private";
    }

    /**
     * Изменение статуса видео на публичное
     * @return string
     */
    public function public(PublicVideoRequest $request, Video $video)
    {
        return "public";
    }

    /**
     * Добавление видео
     * @return string
     */
    public function store(VideoAddRequest $request)
    {
        $user = Video::create([
                'photo_file' => $request->photo_file ? $request->photo_file->store('video_previews') : null,
                'video_file' => $request->video_file ? $request->video_file->store('user_videos') : null,
                'user_id' => $request->user()->id] + $request->all()
        );
        return response()->json([
            'data' => [
                'id' => $user->id,
                'status' => 'created'
            ]
        ])->setStatusCode(201, 'Created');
    }

    /**
     * Изменение содержимого видео
     * @return string
     */
    public function update(VideoUpdateRequest $request, Video $video)
    {
        return "update";
    }

    /**
     * Добавление тега к видео
     * @return string
     */
    public function store_tag(TagAddRequest $request, Video $video, Tag $tag)
    {
        return "add tag";
    }

    /**
     * Удаление тега у видео
     * @param TagDeleteRequest $request
     * @param Video $video
     * @param Tag $tag
     * @return string
     */
    public function destroy_tag(TagDeleteRequest $request, Video $video, Tag $tag)
    {
        return "add tag";
    }

    /**
     * Удаление видео
     * @return string
     */
    public function destroy(VideoDeleteRequest $request, Video $video)
    {
        return "destroy";
    }
}
