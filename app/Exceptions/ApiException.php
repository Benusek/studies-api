<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiException extends HttpResponseException
{
    public function __construct($code = 422, $message = 'Validation error', $errors = [])
    {
        $data = [
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ];
        if (count($errors) > 0) {
            $data['error']['errors'] = $errors;
        }
        $response = response()->json($data, $code);
        parent::__construct($response);
    }
}
