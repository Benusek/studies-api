<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerAddRequest;
use App\Http\Requests\CommentAddRequest;
use App\Http\Requests\CommentChangeRequest;
use App\Http\Requests\CommentDeleteRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\ReportVideo;
use App\Models\Video;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Вывод всех комментариев видео
     * @param Video $video
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Video $video)
    {
        return CommentResource::collection($video->comments);
    }

    /**
     * Добавление комментария к видео
     * @return string
     */
    public function store(CommentAddRequest $request, Video $video) {
        Comment::create([
            'video_id' => $video->id,
            'user_id' => $request->user('api')->id
        ] + $request->all());

        return response()->json([
            'data' => [
                'id' => $video->id,
                'status' => 'commented',
            ]
        ]);
    }

    /**
     * Изменение комментария
     * @return string
     */
    public function update(CommentChangeRequest $request, Comment $comment)
    {
        $comment->update($request->all());
        return response()->json([
            'data' => [
                'id' => $comment->id,
                'status' => 'changed',
            ]
        ]);
    }

    /**
     * Добавление подкомментария к комментарию
     * @return string
     */
    public function store_answer(AnswerAddRequest $request)
    {
        return "store comment";
    }

    /**
     * Удаление комментария
     * @return string
     */
    public function destroy(CommentDeleteRequest $request, Comment $comment)
    {
        return "destroy comment";
    }
}
