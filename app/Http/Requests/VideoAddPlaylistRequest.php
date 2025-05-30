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
        parent::action($this->playlist,'add', 'video to this playlist');

        //Пользователь не может повторно добавить видео в плейлист
        $video = PlaylistVideo::where([
            'video_id' => $this->video->id,
            'playlist_id' => $this->playlist->id
        ])->first();
        if ($video !== null) {
            throw new ApiException(402, 'This video already exists in this playlist');
        }

        //Пользователь не может добавлять непубличное видео в плейлист
        //Eсли видео приватное и опубликовано пользователем, то может
        $video = Video::where('id', '=', $this->input('video_id'))->first();
        if ($video !== null) {
            if ($video->public === 0 && $this->user()->id !== $video->user_id) {
                throw new ApiException(402, 'This video is not public');
            }
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
