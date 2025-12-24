<?php

namespace App\Http\Requests;

class UserLoginRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'login' => 'required|string',
            'password' => 'required|string'
        ];
    }
}
