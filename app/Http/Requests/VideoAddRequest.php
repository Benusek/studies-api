<?php

namespace App\Http\Requests;

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
            'title' => 'required|string|unique:videos,title',
            'description' => 'required|string',
            'thumbnail' => 'image|mimes:jpeg,png,jpg|dimensions:ratio=16/9,min_height=720,min_width=1280',
            'video' => 'required|file|mimes:mp4,mov,ogg,webm',
            'category_id' => 'exists:categories,id',
        ];
    }
}
