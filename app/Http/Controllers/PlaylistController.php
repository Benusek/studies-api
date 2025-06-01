<?php

namespace App\Http\Controllers;

use App\Http\Requests\OtherPlaylistAddRequest;
use App\Http\Requests\OtherPlaylistDeleteRequest;
use App\Http\Requests\PlaylistAddRequest;
use App\Http\Requests\PlaylistDeleteRequest;
use App\Http\Requests\PlaylistUpdateRequest;
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
use App\Models\UserPlaylist;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{

    /**
     * Создание своего плейлиста
     * @param PlaylistAddRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PlaylistAddRequest $request)
    {
        $playlist = Playlist::create(['user_id' => Auth::id()] + $request->all());
        return parent::response($playlist, 'created');
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
        return parent::response($playlist, 'added');
    }

    /**
     * Просмотр плейлистов пользователя
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function show(Request $request, User $user)
    {
        $my_playlists = $request->user('api')->playlists;
        $collection_playlists = Playlist::whereIn('id', $request->user('api')->other_playlists->pluck('playlist_id'))->get();
        $all = $my_playlists->concat($collection_playlists);
        if ($request->user('api')->id === $user->id) {
            return PlaylistResource::collection($all);
        }
        return PlaylistResource::collection(Playlist::where([
            'user_id' => $user->id,
            'public' => 1
        ])->get());
    }

    /**
     * Обновление заголовка
     * @param PlaylistUpdateRequest $request
     * @param Playlist $playlist
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PlaylistUpdateRequest $request, Playlist $playlist)
    {
        $playlist->update($request->all());
        return parent::response($playlist, 'updated');
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
     * Изменение статус плейлиста на приватный
     * @param PrivatePlaylistRequest $request
     * @param Playlist $playlist
     * @return \Illuminate\Http\JsonResponse
     */
    public function private(PrivatePlaylistRequest $request, Playlist $playlist)
    {
        UserPlaylist::where('playlist_id', '=', $playlist->id)->delete();
        return parent::status($playlist, 0);
    }

    public function store_other_playlist(OtherPlaylistAddRequest $request, Playlist $playlist)
    {
        UserPlaylist::create(['user_id' => $request->user('api')->id, 'playlist_id' => $playlist->id]);
        return parent::response($playlist, 'added to collection');
    }

    public function destroy_other_playlist(OtherPlaylistDeleteRequest $request, Playlist $playlist)
    {
        UserPlaylist::where(['user_id' => $request->user('api')->id, 'playlist_id' => $playlist->id])->delete();
        return parent::response($playlist, 'deleted from collection');
    }

    /**
     * Удаление плейлиста
     * @param PlaylistDeleteRequest $request
     * @param Playlist $playlist
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PlaylistDeleteRequest $request, Playlist $playlist)
    {
        return parent::delete($playlist);
    }

    /**
     * Удаление видео из плейлиста
     * @param VideoDeletePlaylistRequest $request
     * @param Playlist $playlist
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy_video(VideoDeletePlaylistRequest $request, Playlist $playlist, Video $video)
    {
        PlaylistVideo::where([
            'playlist_id' => $playlist->id,
            'video_id' => $video->id
        ])->delete();
        return parent::response($video, 'deleted from playlist');
    }
}
