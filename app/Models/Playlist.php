<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'public',
        'user_id',
        'title'
    ];
    /**
     * Множество плейлистов могут быть созданы одним пользователем
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Один плейлист может иметь множество видео
     * Таблица playlists связана с таблицей videos через таблицу video_playlists
     * через вторичные ключи video_id и playlist_id
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function videos()
    {
        return $this->hasManyThrough(Video::class, PlaylistVideo::class, 'playlist_id', 'id', 'id', 'video_id');
    }
}
