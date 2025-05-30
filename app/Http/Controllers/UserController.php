<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
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
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

//    /**
//     * Регистрация нового пользователя
//     * @return string
//     */
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
