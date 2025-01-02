<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Models\AppConfiguration;
use Illuminate\Foundation\Http\FormRequest;

class LoginOTPRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return AppConfiguration::getByCode(User::CAN_USE_OTP_CONF)->value;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ];
    }
}
