<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;

class UserUpdateRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** Other profile */
        if ($this->user->id !== auth('api')->id()) {
            throw new ApiException(402, "You are not allowed to update this user.");
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => "string",
            'surname' => "string",
            'patronymic' => "string",
            'login' => "string|unique:users",
            'password' => "string",
            'email' => "string|email",
            'photo_file' => 'image|mimes:jpeg,png,jpg',
        ];
    }
}
