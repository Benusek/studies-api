<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;

class UserShowRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->user->id !== $this->user()->id) {
            throw new ApiException(402, 'You are not allowed to access this resource');
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
