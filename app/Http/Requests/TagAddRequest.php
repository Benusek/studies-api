<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use App\Models\TagVideo;
use Illuminate\Foundation\Http\FormRequest;

class TagAddRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Пользователь не может добавить тег не к своему видео
        parent::action($this->video, 'add tags', 'to this video');

        //Пользователь не может добавить уже содержащий тег видео,
        $tag = TagVideo::where([
            'video_id' => $this->video->id,
            'tag_id' => $this->tag->id
        ])->first();
        if ($tag !== null) {
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
