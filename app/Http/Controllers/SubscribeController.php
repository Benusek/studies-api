<?php

namespace App\Http\Controllers;

use App\Models\Subscribe;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    /**
     * Просмотр подписчиков пользователя
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Subscribe::all();
    }

    /**
     * Подписаться на пользователя
     * @return string
     */
    public function store()
    {
        return "store";
    }

    /**
     * Отписаться от пользователя
     * @return string
     */
    public function destroy()
    {
        return "destroy";
    }
}
