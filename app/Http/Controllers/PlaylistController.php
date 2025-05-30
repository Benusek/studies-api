<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistAddRequest;
use App\Http\Requests\PlaylistDeleteRequest;
use App\Http\Requests\PrivatePlaylistRequest;
use App\Http\Requests\PublicPlaylistRequest;
use App\Http\Requests\VideoAddPlaylistRequest;
use App\Http\Requests\VideoDeletePlaylistRequest;
use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;

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
     * @return string
     */
    public function store(PlaylistAddRequest $request)
    {
        return "store playlist";
    }

    /**
     * Добавление видео в плейлист
     * @return string
     */
    public function store_video(VideoAddPlaylistRequest $request, Playlist $playlist, Video $video)
    {
        return "store video";
    }

    /**
     * Просмотр публичных плейлистов пользователя
     * @return string
     */
    public function show()
    {
        return "show playlist";
    }

    /**
     * Изменение статус плейлиста на публичный
     * @return string
     */
    public function public(PublicPlaylistRequest $request, Playlist $playlist)
    {
        return "public playlist";
    }

    /**
     * Изменение статус плейлиста на привытный
     * @return string
     */
    public function private(PrivatePlaylistRequest $request, Playlist $playlist)
    {
        return "private playlist";
    }

    /**
     * Удаление плейлиста
     * @return string
     */
    public function destroy(PlaylistDeleteRequest $request, Playlist $playlist)
    {
        return "destroy playlist";
    }

    /**
     * Удаление видео из плейлиста
     * @return string
     */
    public function destroy_video(VideoDeletePlaylistRequest $request, Playlist $playlist, Video $video)
    {
        return "destroy video";
    }
}
