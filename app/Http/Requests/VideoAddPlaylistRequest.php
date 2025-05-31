<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use App\Models\PlaylistVideo;
use App\Models\Video;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class VideoAddPlaylistRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::action($this->playlist, 'add', 'video to this playlist');

        //Пользователь не может повторно добавить видео в плейлист
        if ($this->playlist->videos->where('id', '=', $this->video->id)->first() !== null) {
            throw new ApiException(402, 'This video already exists in this playlist');
        }

        //Пользователь не может добавлять непубличное видео в плейлист
        //Eсли видео приватное и опубликовано пользователем, то может
        if ($this->video->public === 0 && $this->user()->id !== $this->video->user_id) {
            throw new ApiException(402, 'This video is not public');
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
