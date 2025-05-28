<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Просмотр всех видео
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Video::all();
    }

    /**
     * Просмотр своих видео
     * @return string
     */
    public function self_index()
    {
        return 'self videos';
    }

    /**
     * Изменение статуса видео на приватное
     * @return string
     */
    public function private()
    {
        return "private";
    }

    /**
     * Изменение статуса видео на публичное
     * @return string
     */
    public function public()
    {
        return "public";
    }

    /**
     * Добавление видео
     * @return string
     */
    public function store()
    {
        return "store";
    }

    /**
     * Изменение содержимого видео
     * @return string
     */
    public function update()
    {
        return "update";
    }

    /**
     * Добавление тэга к видео
     * @return string
     */
    public function tag()
    {
        return "add tag";
    }

    /**
     * Удаление видео
     * @return string
     */
    public function destroy()
    {
        return "destroy";
    }
}
