<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserShowRequest;
use App\Http\Requests\UserUpdateRequest;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    /**
     * Просмотр всех пользователей
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($start, $count)
    {
        return UserResource::collection(User::get()->slice($start, $count));
    }

    /**
     * Просмотр пользователя
     * @param UserShowRequest $request
     * @param User $user
     * @return UserResource
     */
    public function show(UserShowRequest $request) {
        return UserResource::make($request->user());
    }

    /**
     * Авторизация
     * @param UserLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(UserLoginRequest $request)
    {
        $user = User::where([
            'login' => $request->login
        ])->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new ApiException(401, 'Неверный логин или пароль');
        }

        return response()->json([
            'data' => [
                'user' => UserResource::make($user),
                'user_token' => $user->generateToken(),
                'message' => 'Успешный вход'
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
            'photo_file' => $request->photo_file ? $request->photo_file->store('avatars') : null] + $request->all()
        );
//        event(new Registered($user));
        return parent::response($user, 'created', 'Вы успешно зарегистрировались')->setStatusCode(201, 'Created');
    }

    /**
     *
     * @param UserUpdateRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        if ($request->email !== null && $request->email !== $user->email) {
            $user->update(request()->all() + ['email_verified_at' => null, 'photo_file' => $request->photo_file ? $request->photo_file->store('avatars') : $user->photo_file]);
            event(new Registered($user));
        }

        if ($user->photo_file) {
            Storage::delete($user->photo_file);
        }

        $user->update(['photo_file' => $request->photo_file ? $request->photo_file->store('avatars') : $user->photo_file] + request()->all());
        return parent::response($user, 'updated', 'Данные успешно обновлены');
    }
}
