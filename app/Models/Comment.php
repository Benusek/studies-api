<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * Множество комментарием могут быть написаны один пользователем
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Множество комментариев могут написаны под одним видео
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function video() {
        return $this->belongsTo(Video::class);
    }

    /**
     * Один комментарий может иметь множество ответов
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comment_answers() {
        return $this->hasMany(CommentAnswer::class);
    }
}
