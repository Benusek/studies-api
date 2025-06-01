<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;

class TagDeleteRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        parent::action($this->video, 'delete tags', 'from this video');
        //Пользователь не может удалить тег, который уже отсутствует у видео
        if ($this->video->tags
                ->where('video_id', '=', $this->video->id)
                ->where('tag_id', '=', $this->tag->id)->first() === null) {
            throw new ApiException(402, "Tag doesn't exist in this video");
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
