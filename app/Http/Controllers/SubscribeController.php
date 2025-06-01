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
     * Подписаться на пользователя
     * @param FollowRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FollowRequest $request, User $user)
    {
        Subscribe::create([
            'user_id' => $user->id,
            'subscriber_id' => $request->user('api')->id
        ]);
        return response()->json([
            'data' => [
                'id' => $user->id,
                'status' => 'follow',
            ]
        ]);
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
            'subscriber_id' => $request->user('api')->id
        ])->delete();
        return response()->json([
            'data' => [
                'id' => $user->id,
                'status' => 'unfollow',
            ]
        ]);
    }
}
