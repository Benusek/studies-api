<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerAddRequest;
use App\Http\Requests\CommentAddRequest;
use App\Http\Requests\CommentChangeRequest;
use App\Http\Requests\CommentDeleteRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Вывод всех комментариев видео
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Comment::all();
    }

    /**
     * Добавление комментария к видео
     * @return string
     */
    public function store(CommentAddRequest $request) {
        return 'store comment';
    }

    /**
     * Изменение комментария
     * @return string
     */
    public function update(CommentChangeRequest $request)
    {
        return "update comment";
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
