<?php

namespace App\Http\Controllers;

use App\Http\Requests\FollowRequest;
use App\Http\Requests\UnfollowRequest;
use App\Models\Subscribe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{

    /**
     * Подписаться на пользователя
     * @param FollowRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FollowRequest $request, User $user)
    {
        Subscribe::create([
            'user_id' => $user->id,
            'subscriber_id' => Auth::id()
        ]);
        return parent::response($user, 'follow');
    }

    /**
     * Отписаться от пользователя
     * @param UnfollowRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(UnfollowRequest $request, User $user)
    {
        Subscribe::where([
            'user_id' => $user->id,
            'subscriber_id' => Auth::id()
        ])->delete();
        return parent::response($user, 'unfollow');
    }
}
