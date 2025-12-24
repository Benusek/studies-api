<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    public static function action(object $object, $method, $obj_message) {
        //Пользователь не может удалить чужое видео
        if ($object->user_id !== auth('api')->id()) {
            throw new ApiException(402, "You are not allowed to {$method} {$obj_message}.");
        }
    }

    public static function status(object $object, $message_obj, $status)
    {
        //Пользователь не может сменить статус чужого объекта
        ApiRequest::action($object, 'change status', "of this {$message_obj}");

        //Пользователь не может менять статус на такой же
        if ($object->public === $status) {
            switch ($status) {
                case 1:
                    $status_message = 'public';
                    break;
                case 0:
                    $status_message = 'private';
                    break;
            }
            throw new ApiException(402, "This {$message_obj} is already {$status_message}");
        }
    }

    public static function private($object, $message_obj) {
        //Пользователь не может ... чужое приватное ...
        if (!$object->public && !auth('api')->user() || !$object->public && $object->user_id !== auth('api')->id()) {
            throw new ApiException(402, "This {$message_obj} is not public");
        }
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ApiException(422, 'Ошибка валидации', $validator->errors());
    }
}
