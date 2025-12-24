<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Models\Subscribe;
use App\Models\User;

class SubscribeController extends Controller
{

    /**
     * Subscribe on channel
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(User $user)
    {
        /** User can't subscribe on his channel **/
        if (auth('api')->id() === $user->id) {
            throw new ApiException(402, 'You are not allowed to follow yourself.');
        }
        /** User can't double subscribe **/
        if (!empty(auth('api')->user()->subscribe) && auth('api')->user()->subscribe->where('user_id', $user->id)->first()) {
            throw new ApiException(402, 'You already subscribed to this channel');
        }

        Subscribe::create([
            'user_id' => $user->id,
            'subscriber_id' => auth('api')->id()
        ]);
        return response()->json(['message' => 'Subscribed successfully', 'status' => true]);
    }

    /**
     * Unsubscribe channel
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        /** User can't follow if not follow this channel **/
        if (auth('api')->check() && !$user->subscribers->contains('id', auth('api')->id())) {
            throw new ApiException(402, 'You are not following this user.');
        }

        Subscribe::where([
            'user_id' => $user->id,
            'subscriber_id' => auth('api')->id()
        ])->delete();
        return response()->json(['message' => 'Unsubscribed successfully', 'status' => true]);
    }
}
