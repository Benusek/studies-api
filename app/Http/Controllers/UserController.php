<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserShowRequest;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Comment;
use App\Models\CommentAnswer;
use App\Models\Playlist;
use App\Models\Subscribe;
use App\Models\Tag;
use App\Models\TagVideo;
use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    /**
     * Просмотр всех пользователей
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function show(UserShowRequest $request, User $user) {
        return UserResource::make($user);
    }

    /**
     * Авторизация
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request)
    {
        $user = User::where([
            'login' => $request->login,
            'password' => $request->password
        ])->first();
        if (!$user) {
            throw new ApiException(401, 'Authentication failed');
        }

        return response()->json([
            'data' => [
                'user_token' => $user->generateToken(),
                'role_id' => $user->role_id
            ]
        ]);
    }

    /**
     * Выход
     * @return array[]
     */
    public function logout()
    {
        Auth::user()->logout();
        return [
            'data' => [
            'message' => 'logout'
            ]
        ];
    }

    /**
     * Регистрация нового пользователя
     * @param UserRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRegisterRequest $request)
    {
        $user = User::create([
            'photo_file' => $request->photo_file ? $request->photo_file->store('user_photos') : null] + $request->all()
        );
        event(new Registered($user));
        return response()->json([
            'data' => [
                'id' => $user->id,
                'status' => 'created'
            ]
        ])->setStatusCode(201, 'Created');
    }
}
