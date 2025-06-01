<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportVideo extends Model
{
    use HasFactory;


    protected $hidden = [
      'updated_at'
    ];

    protected $fillable = [
      'video_id',
      'user_id',
      'report_id'
    ];
    /**
     * Множество жалоб может быть от одного пользователя
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Множество жалоб может быть адресовано одному видео
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function video()
    {
        return $this->belongsTo(Video::class);
    }


    public function report() {
        return $this->belongsTo(Report::class);
    }
}
