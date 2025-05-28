<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\CommentAnswer;
use App\Models\Playlist;
use App\Models\Report;
use App\Models\Subscribe;
use App\Models\Tag;
use App\Models\TagVideo;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Просмотр всех пользователей
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        dd(Video::where('id', '=', '1')->first()->playlists);
        return User::all();
    }

    /**
     * Авторизация
     * @return string
     */
    public function login()
    {
        return "login";
    }

    /**
     * Выход
     * @return string
     */
    public function logout()
    {
        return "logout";
    }

    /**
     * Регистрация нового пользователя
     * @return string
     */
    public function store()
    {
        return "store_user";
    }
}
