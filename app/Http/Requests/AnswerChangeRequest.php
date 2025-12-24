<?php

namespace App\Http\Requests;

class AnswerChangeRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::action($this->answer->comment->video, 'update', 'this answer');
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'text' => 'required|string'
        ];
    }
}
