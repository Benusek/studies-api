<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use App\Models\Video;

class CommentAddRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Пользователь не может оставлять комментарий на приватное видео
        $video = Video::where('id', '=', $this->input('video_id'))->first();
        if ($video !== null) {
            if ($video->public === 0) {
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
            'text' => 'required|string'
        ];
    }
}
