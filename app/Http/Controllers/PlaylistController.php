<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistAddRequest;
use App\Http\Requests\PlaylistDeleteRequest;
use App\Http\Requests\PrivatePlaylistRequest;
use App\Http\Requests\PublicPlaylistRequest;
use App\Http\Requests\VideoAddPlaylistRequest;
use App\Http\Requests\VideoDeletePlaylistRequest;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\VideoResource;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\Subscribe;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    /**
     * Вывод своих плейлистов
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Playlist::all();
    }

    /**
     * Создание своего плейлиста
     * @param PlaylistAddRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PlaylistAddRequest $request)
    {
        $playlist = Playlist::create(['user_id' => Auth::id()] + $request->all());
        return response()->json([
            'data' => [
                'id' => $playlist->id,
                'status' => 'created',
            ]
        ]);
    }

    /**
     * Добавление видео в плейлист
     * @param VideoAddPlaylistRequest $request
     * @param Playlist $playlist
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_video(VideoAddPlaylistRequest $request, Playlist $playlist, Video $video)
    {
        PlaylistVideo::create([
            'playlist_id' => $playlist->id,
            'video_id' => $video->id
        ]);
        return response()->json([
            'data' => [
                'id' => $playlist->id,
                'status' => 'added',
            ]
        ]);
    }

    /**
     * Просмотр плейлистов пользователя
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Request $request, User $user)
    {
        if ($request->user('api')->id === $user->id) {
            return PlaylistResource::collection(Playlist::where(['user_id' => $user->id])->get());
        }
        return PlaylistResource::collection(Playlist::where([
            'user_id' => $user->id,
            'public' => 1
        ])->get());
    }

    /**
     * Изменение статус плейлиста на публичный
     * @param PublicPlaylistRequest $request
     * @param Playlist $playlist
     * @return \Illuminate\Http\JsonResponse
     */
    public function public(PublicPlaylistRequest $request, Playlist $playlist)
    {
        return parent::status($playlist, 1);
    }

    /**
     * Изменение статус плейлиста на привытный
     * @param PrivatePlaylistRequest $request
     * @param Playlist $playlist
     * @return \Illuminate\Http\JsonResponse
     */
    public function private(PrivatePlaylistRequest $request, Playlist $playlist)
    {
        return parent::status($playlist, 0);
    }

    /**
     * Удаление плейлиста
     * @return string
     */
    public function destroy(PlaylistDeleteRequest $request, Playlist $playlist)
    {
        $playlist->delete();
        return response()->json([
            'data' => [
                'id' => $playlist->id,
                'status' => 'deleted',
            ]
        ]);
    }

    /**
     * Удаление видео из плейлиста
     * @return string
     */
    public function destroy_video(VideoDeletePlaylistRequest $request, Playlist $playlist, Video $video)
    {
        PlaylistVideo::where([
            'playlist_id' => $playlist->id,
            'video_id' => $video->id
        ])->delete();
        return response()->json([
            'data' => [
                'id' => $playlist->id,
                'video_id' => $video->id,
                'status' => 'deleted',
            ]
        ]);
    }
}
