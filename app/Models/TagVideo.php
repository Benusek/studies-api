<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagVideo extends Model
{
    use HasFactory;
    protected $fillable = [
        'tag_id',
        'video_id',
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
