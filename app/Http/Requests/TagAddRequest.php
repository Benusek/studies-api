<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;

class TagAddRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Пользователь не может добавить тег не к своему видео
        parent::action($this->video, 'add tags', 'to this video');

        //Пользователь не может добавить уже содержащий тег видео
        if ($this->video->tags->where('tag_id', '=', $this->tag->id)->first() !== null) {
            throw new ApiException(402, 'Video already exists this tag');
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
