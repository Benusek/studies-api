<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerAddRequest;
use App\Http\Requests\AnswerChangeRequest;
use App\Http\Requests\CommentAddRequest;
use App\Http\Requests\CommentChangeRequest;
use App\Http\Requests\CommentDeleteRequest;
use App\Http\Requests\CommentShowRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\CommentAnswer;
use App\Models\ReportVideo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Вывод всех комментариев видео
     * @param Video $video
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(CommentShowRequest $request, Video $video)
    {
        return CommentResource::collection($video->comments);
    }

    /**
     * Добавление комментария к видео
     * @param CommentAddRequest $request
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentAddRequest $request, Video $video) {
        Comment::create([
            'video_id' => $video->id,
            'user_id' => $request->user('api')->id,
        ] + $request->all());
        return parent::response($video, 'commented');
    }

    /**
     * Изменение комментария
     * @param CommentChangeRequest $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CommentChangeRequest $request, Comment $comment)
    {
        $comment->update($request->all());
        return parent::response($comment, 'changed');
    }

    /**
     * Добавление ответа к комментарию
     * @param AnswerAddRequest $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_answer(AnswerAddRequest $request, Comment $comment)
    {
        CommentAnswer::create( $request->all() + [
                'user_id' => Auth::id(),
                'comment_id' => $comment->id
            ]);
        return parent::response($comment, 'answered');
    }

    /**
     * Изменение ответа
     * @param AnswerChangeRequest $request
     * @param CommentAnswer $answer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_answer(AnswerChangeRequest $request, CommentAnswer $answer)
    {
        $answer->update($request->all());
        return parent::response($answer, 'updated');
    }

    /**
     * Удаление комментария
     * @param CommentDeleteRequest $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CommentDeleteRequest $request, Comment $comment)
    {
        return parent::delete($comment);
    }
}
