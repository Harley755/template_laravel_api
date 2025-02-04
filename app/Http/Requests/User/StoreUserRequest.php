<?php

namespace App\Http\Requests\User;

use App\Traits\Requests\Requestable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    use Requestable;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('user_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "firstname" => "required|string|max:255",
            "lastname" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users,email",
            "phone_number" => "required|string|max:255|unique:users,phone_number",

            "roles" => "array|nullable",
            "roles.*" => "string|exists:roles,alias",

            "avatar" => "nullable|file|max:7000"
        ];
    }
}
