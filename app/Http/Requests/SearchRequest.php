<?php

namespace App\Http\Requests;

class SearchRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'str' => 'required|string',
            'categories' => 'array',
            'tags' => 'array',
            'type' => 'required|string',
        ];
    }
}
