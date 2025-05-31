<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowRequest;
use App\Http\Requests\UnfollowRequest;
use App\Models\Subscribe;
use App\Models\User;
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
    public function store(FollowRequest $request, User $user)
    {
        return "store";
    }

    /**
     * Отписаться от пользователя
     * @return string
     */
    public function destroy(UnfollowRequest $request, User $user)
    {
        return "destroy";
    }
}
