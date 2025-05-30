<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoAddRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'photo_file' => 'image|mimes:jpeg,png,jpg|dimensions:ratio=16/9,min_height=720,min_width=1280',
            'video_file' => 'required|video|mimes:mp4,mov,ogg,webm'
        ];
    }
}
