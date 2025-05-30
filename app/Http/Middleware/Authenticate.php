<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new ApiException(403, 'Unauthenticated');
    }
}
