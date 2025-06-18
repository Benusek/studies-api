<?php

namespace App\Http\Requests;

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
            'login' => "required|string|unique:users|regex:/^[a-zA-Z0-9]+$/|min:3",
            'password' => "required|string",
            'email' => "required|string|email",
            'photo_file' => 'image|mimes:jpeg,png,jpg',
        ];
    }
}
