<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Foundation\Http\FormRequest;

class OtherPlaylistAddRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Пользователь не может добавить плейлист в коллекцию, если он уже присутствует в ней
        if ($this->playlist->added->where('user_id', '=', $this->user('api')->id)->count() !== 0) {
            throw new ApiException(402, 'This playlist is already added');
        }

        //Пользователь не может добавить приватный плейлист
        parent::private($this->playlist, 'playlist');

        //Пользователь не может добавить свой плейлист
        if ($this->playlist->user_id === $this->user('api')->id) {
             throw new ApiException(402, 'You are not allowed to add yourself playlists');
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
