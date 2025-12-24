<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\AnswerAddRequest;
use App\Http\Requests\AnswerChangeRequest;
use App\Http\Requests\ApiRequest;
use App\Http\Requests\CommentAddRequest;
use App\Http\Requests\CommentChangeRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\CommentAnswer;
use App\Models\Video;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Get all comments of video
     * @param $id
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Video $video)
    {
        ApiRequest::private($video, 'video');
        return CommentResource::collection($video->comments);
    }

    /**
     * Add comment
     * @param CommentAddRequest $request
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CommentAddRequest $request, Video $video) {
        Comment::create([
            'video_id' => $video->id,
            'user_id' => auth('api')->id(),
        ] + $request->all());
        return parent::response($video, 'commented');
    }

    /**
     * Change comment
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
     * Add answer
     * @param AnswerAddRequest $request
     * @param Comment $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_answer(AnswerAddRequest $request, Comment $comment)
    {
        CommentAnswer::create( $request->all() + [
                'comment_id' => $comment->id,
                'user_id' => auth('api')->id()
            ]);
        return parent::response($comment, 'answered');
    }

    /**
     * Change answer
     * @param AnswerChangeRequest $request
     * @param CommentAnswer $answer
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_answer(Request $request, CommentAnswer  $answer)
    {
        ApiRequest::action($request->answer->comment->video, 'update', 'this answer');
        $answer->update($request->all());
        return parent::response($answer, 'updated');
    }

    /**
     * Delete comment
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Comment $comment)
    {
        //Пользователь не может удалять чужой комментарий, если он не написан под его видео
        if (auth('api')->id() !== $comment->user_id && auth('api')->id() !== $comment->video->user_id) {
            throw new ApiException(402, 'You are not allowed to delete this comments');
        }
        return parent::delete($comment);
    }
}
