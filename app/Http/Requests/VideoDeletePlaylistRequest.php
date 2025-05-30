<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use App\Models\PlaylistVideo;
use Illuminate\Foundation\Http\FormRequest;

class VideoDeletePlaylistRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::action($this->playlist,'delete', 'video from this playlist');
        //Пользователь не может удалять видео, которое не находится в плейлисте
        $video = PlaylistVideo::where([
            'video_id' => $this->video->id,
            'playlist_id' => $this->playlist->id
        ])->first();
        if ($video === null) {
            throw new ApiException(402, "This video don't exists in this playlist");
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
