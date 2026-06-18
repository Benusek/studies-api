<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistAddRequest;
use App\Http\Requests\PlaylistUpdateRequest;
use App\Http\Resources\ChannelResource;
use App\Http\Resources\PlaylistResource;
use App\Http\Resources\VideoResource;
use App\Models\Playlist;
use App\Models\PlaylistVideo;
use App\Models\User;
use App\Models\UserPlaylist;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{

    /**
     * Create playlist
     * @param PlaylistAddRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PlaylistAddRequest $request)
    {
        $playlist = Playlist::create(['user_id' => Auth::id()] + $request->all());
        return parent::response($playlist, 'created', 'Плейлист успешно создан!');
    }

    public function show_videos(Playlist $playlist) {
        return response()->json([
            'videos'=> VideoResource::collection($playlist->videos()->get()),
            'playlist' => PlaylistResource::make($playlist)
        ]);
    }

    /**
     * Add video in playlist
     * @param Playlist $playlist
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function store_video(Playlist $playlist, Video $video)
    {
        PlaylistVideo::create([
            'playlist_id' => $playlist->id,
            'video_id' => $video->id
        ]);
        return parent::response($playlist, 'added');
    }

    /**
     * Get collection of playlists
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function collection(User $user)
    {
        return PlaylistResource::collection(Playlist::whereIn('id', $user->other_playlists->pluck('playlist_id'))->get());
    }

    /**
     * Get user's playlists
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        /** Get my **/
        if (auth('api')->user() && auth('api')->id() === $user->id) {
            return response()->json([
                'user' => ChannelResource::make($user),
                'playlists' => PlaylistResource::collection($user->playlists)
            ]);
        }
        /** Get other **/
        return response()->json([
            'user' => ChannelResource::make($user),
            'playlists' => PlaylistResource::collection(Playlist::where([
                'user_id' => $user->id,
                'public' => 1
            ])->get())]);


    }

    /**
     * Change title
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
     * Change status on public
     * @param Playlist $playlist
     * @return \Illuminate\Http\JsonResponse
     */
    public function public(Playlist $playlist)
    {
        return parent::status($playlist, 1);
    }

    /**
     * Change status on private
     * @param Playlist $playlist
     * @return \Illuminate\Http\JsonResponse
     */
    public function private(Playlist $playlist)
    {
        UserPlaylist::where('playlist_id', '=', $playlist->id)->delete();
        return parent::status($playlist, 0);
    }

    public function store_other(Playlist $playlist)
    {
        UserPlaylist::create(['user_id' => auth('api')->id(), 'playlist_id' => $playlist->id]);
        return parent::response($playlist, 'added to collection');
    }

    public function destroy_other(Playlist $playlist)
    {
        UserPlaylist::where(['user_id' => auth('api')->id(), 'playlist_id' => $playlist->id])->delete();
        return parent::response($playlist, 'deleted from collection');
    }

    /**
     * Delete playlist
     * @param Playlist $playlist
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Playlist $playlist)
    {
        return parent::delete($playlist);
    }

    /**
     * Delete video from playlist
     * @param Playlist $playlist
     * @param Video $video
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy_video(Playlist $playlist, Video $video)
    {
        PlaylistVideo::where([
            'playlist_id' => $playlist->id,
            'video_id' => $video->id
        ])->delete();
        return parent::response($video, 'deleted from playlist');
    }
}
