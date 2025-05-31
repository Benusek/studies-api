<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use App\Models\Report;
use Illuminate\Foundation\Http\FormRequest;

class ReportAddRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //Пользователь не может отправить больше одной жалобы на одно видео
        if ($this->video->reports->where('user_id', '=', $this->user()->id)->first()) {
            throw new ApiException(402, 'You already send a report on this video');
        }
        //Пользователь не может отправить жалобу на собственное видео
        if ($this->video->user_id === $this->user()->id) {
            throw new ApiException(402, 'You are not allowed to send a report on your video');
        }

        //Пользователь не может отправить жалобу на приватное видео
        if ($this->video->public === 0) {
            throw new ApiException(402, 'You are not allowed to send a report on this video');
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
