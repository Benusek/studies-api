<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;

class CommentDeleteRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Пользователь не может удалять чужой комментарий, если он не написан под его видео
        if ($this->user()->id !== $this->comment->user_id && $this->user()->id !== $this->comment->video->user_id) {
            throw new ApiException(402, 'You are not allowed to delete this comments');
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
            //
        ];
    }
}
