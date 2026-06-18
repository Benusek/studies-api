<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $hidden = [
        'updated_at'
    ];

    protected $fillable = [
        'title',
        'description',
        'thumbnail',
        'video',
        'duration',
        'user_id',
        'public',
        'category_id'
    ];

    /**
     * Множество видео могут быть опубликованы одним пользователем
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Одно видео может иметь множество тегов
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'tag_videos',
            'video_id',
            'tag_id'
        );
    }

    /**
     * Одно видео может иметь множество комментариев
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments() {
        return $this->hasMany(Comment::class);
    }

    /**
     * Одно видео может находиться в множестве плейлистов
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playlists()
    {
        return $this->hasMany(PlaylistVideo::class);
    }

    /**
     * Множество видео могут иметь одну категорию
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
