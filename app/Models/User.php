<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'remember_token',
        'login',
        'surname',
        'patronymic',
        'email',
        'photo_file',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'role_id',
        'password',
        'email',
        'email_verified_at',
        'patronymic',
        'surname',
        'photo_file',
        'remember_token',
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Множество пользователей могут иметь одну роль
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roles)
    {
        return in_array($this->role->code, $roles);
    }

    /**
     * Один пользователь может иметь многих подписчиков
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribers() {
        return $this->hasMany(Subscribe::class, 'user_id');
    }

    /**
     * Один пользователь может быть подписан на многих
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscribe() {
        return $this->hasMany(Subscribe::class, 'subscriber_id');
    }

    /**
     * Один пользователь может иметь множество видео
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function videos() {
        return $this->hasMany(Video::class);
    }

    /**
     * Один пользователь может иметь множество плейлистов
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function playlists() {
        return $this->hasMany(Playlist::class);
    }

    /**
     * Один пользователь может добавить себе множество плейлистов
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function other_playlists() {
        return $this->hasMany(UserPlaylist::class);
    }

    /**
     * Один пользователь может отправить множество жалоб
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(ReportVideo::class);
    }

    public function generateToken()
    {
        $this->update([
            'remember_token' => Str::random(25)
        ]);
        return $this->remember_token;
    }

    public function logout()
    {
        $this->update([
            'remember_token' => null
        ]);
    }
}
