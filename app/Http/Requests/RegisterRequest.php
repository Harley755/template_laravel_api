<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => ['nullable', 'string'],
            'lastname' => ['nullable', 'string'],

            'phone_number' => ['required', 'string', 'unique:users,phone_number'],

            'email' => ['required', 'string', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            'avatar' => ['nullable', 'file', 'mimes:jpeg,png,jpg', 'max:7000'],
        ];
    }
}
