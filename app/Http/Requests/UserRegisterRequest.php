<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use App\Models\Video;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => "required|string",
            'surname' => "string",
            'patronymic' => "string",
            'login' => "required|string|unique:users",
            'password' => "required|string",
            'email' => "required|string|email",
            'photo_file' => 'image|mimes:jpeg,png,jpg',
        ];
    }
}
