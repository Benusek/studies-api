<?php

namespace App\Http\Requests;

class AnswerAddRequest extends ApiRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        parent::private($this->comment->video, 'video');
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
