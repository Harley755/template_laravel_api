<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\UnauthorizedException;

abstract class Controller
{
    public function handleResponse($result, string $message, int $code = Response::HTTP_OK, bool $success = true)
    {
        // Format response
        $response = [
            "success" => $success,
            "data"    => $result,
            "message" => $message
        ];
        // return response
        return response()->json($response, $code);
    }

    public function checkGate(string $permission)
    {
        if (Gate::denies($permission)) throw new UnauthorizedException('This action is unauthorized.', Response::HTTP_UNAUTHORIZED);
    }
}
