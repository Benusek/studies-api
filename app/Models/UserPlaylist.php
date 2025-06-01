<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlaylist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'playlist_id',
    ];

    /**
     * Множество пользователей могут добавить один плейлист
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Множество плейлистов могут быть добавлены одним пользователем
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playlist() {
        return $this->belongsTo(Playlist::class);
    }
}
