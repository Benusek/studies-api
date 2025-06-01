<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function status($object, $status) {
        $object->update(['public' => $status]);
        return response()->json([
            'data' => [
                'id' => $object->id,
                'status' => 'changed',
            ]
        ]);
    }
}
