<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Change status of model
     * @param $object
     * @param $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($object, $status) {
        $object->update(['public' => $status]);
        return $this->response($object, 'changed');
    }

    /**
     * Delete
     * @param $object
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($object)
    {
        $object->delete();
        return $this->response($object, true, 'success');
    }

    /**
     * Get response
     * @param $object
     * @param $method
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function response($object, $method, $message = null) {
        return response()->json([
            'data' => [
                'id' => $object->id,
                'status' => $method,
                'message' => $message
            ]
        ]);
    }
}
