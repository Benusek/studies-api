<?php

namespace App\Http\Requests;

class PlaylistAddRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return string[]
     */
    public function rules(): array
    {
        return [
            "title" => 'required|string',
            "description" => 'string'
        ];
    }
}
