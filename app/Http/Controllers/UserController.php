<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    /**
     * Get all users
     * @param $start
     * @param $count
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index($start, $count)
    {
        return UserResource::collection(User::get()->slice($start, $count));
    }

    /**
     * Show profile
     * @param User $user
     * @return UserResource
     */
    public function show(User $user) {
        if (auth('api')->id() !== $user->id) {
            throw new ApiException(402, 'You are not allowed to access this resource');
        }
        return UserResource::make($user);
    }

    /**
     * Authorization
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
     * Logout
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
     * Registration
     * @param UserRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRegisterRequest $request)
    {
        $user = User::create([
            'avatar' => $request->photo_file ? $request->photo_file->store('avatars') : null] + $request->all()
        );
        return parent::response($user, 'created', 'Вы успешно зарегистрировались')->setStatusCode(201, 'Created');
    }

    /**
     * @param UserUpdateRequest $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserUpdateRequest $request, User $user)
{
    $data = $request->validated();

    if ($request->hasFile('photo_file')) {

        if ($user->photo_file) {
            Storage::delete($user->photo_file);
        }

        $data['photo_file'] = $request
            ->file('photo_file')
            ->store('avatars', 'local');
    }

    $user->update($data);

    return parent::response(
        $user->fresh(),
        'updated',
        'Данные успешно обновлены'
    );
}
}
