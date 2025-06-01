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
        parent::private($this->video, 'video');
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
