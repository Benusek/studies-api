<?php

namespace App\Http\Requests;

class CommentChangeRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function authorize(): bool
    {
        parent::action($this->comment->user_id, 'update', 'this comment');
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => 'required|string'
        ];
    }
}
