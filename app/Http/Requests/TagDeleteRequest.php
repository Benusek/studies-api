<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TagDeleteRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        faflfflakj
        dd($this->video->tags::where([
            'id' => $this->tag
        ]));
        parent::action($this->video, 'delete tags', 'from this video');

//        if ($this->) {
//
//        }
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
