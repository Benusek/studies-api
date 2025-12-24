<?php

namespace App\Http\Requests;

class VideoUpdateRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::action($this->video,'update', 'this video');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'title' => 'string',
            'description' => 'string',
            'photo_file' => 'image|mimes:jpeg,png,jpg,gif,svg|dimensions:ratio=16/9,min_height=720,min_width=1280',
            'video_file' => 'video|mimes:mp4,mov,ogg,wmv,flv'
        ];
    }
}
