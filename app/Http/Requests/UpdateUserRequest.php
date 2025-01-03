<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('user_edit');
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
            "email" => "required|string|email|max:255|unique:users,email," . $this->user->id,
            "phone_number" => "required|string|max:255|unique:users,phone_number," . $this->user->id,

            "roles" => "array|nullable",
            "roles.*" => "string|exists:roles,alias",

            "avatar" => "nullable|file|mimes:jpeg,png,jpg,gif|max:7000"
        ];
    }
}
