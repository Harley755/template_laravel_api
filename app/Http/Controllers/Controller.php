<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

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
}
