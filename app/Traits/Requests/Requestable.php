<?php

namespace App\Traits\Requests;

use Illuminate\Contracts\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait Requestable
{
    public function failedAuthorization()
    {

        throw new UnauthorizedException('This action is unauthorized.', Response::HTTP_UNAUTHORIZED);
    }

    protected function failedValidation(Validator $validator)
    {
        $data = [
            "errors" => $validator->errors()
        ];
        throw new HttpResponseException(
            response()->json($data, 422)
        );
    }
}
