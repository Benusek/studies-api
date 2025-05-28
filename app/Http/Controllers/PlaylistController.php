<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
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
    public function store()
    {
        return "store playlist";
    }

    /**
     * Добавление видео в плейлист
     * @return string
     */
    public function store_video()
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
    public function public()
    {
        return "public playlist";
    }

    /**
     * Изменение статус плейлиста на привытный
     * @return string
     */
    public function private()
    {
        return "private playlist";
    }

    /**
     * Удаление плейлиста
     * @return string
     */
    public function destroy()
    {
        return "destroy playlist";
    }

    /**
     * Удаление видео из плейлиста
     * @return string
     */
    public function destroy_video()
    {
        return "destroy video";
    }
}
