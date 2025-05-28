<?php

namespace App\Http\Controllers;

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
    public function store() {
        return 'store comment';
    }

    /**
     * Изменение комментария
     * @return string
     */
    public function update()
    {
        return "update comment";
    }

    /**
     * Добавление подкомментария к комментарию
     * @return string
     */
    public function store_answer()
    {
        return "store comment";
    }

    /**
     * Удаление комментария
     * @return string
     */
    public function destroy()
    {
        return "destroy comment";
    }
}
