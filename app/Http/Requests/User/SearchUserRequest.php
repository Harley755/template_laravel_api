<?php

namespace App\Http\Requests\User;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class SearchUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('user_search');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'periode' => "nullable|array",
            'periode.from' => "nullable|date",
            'periode.to' => "nullable|date",

            'email' => "nullable|string|max:255",
            'phone_number' => "nullable|string|max:255",
            'firstname' => "nullable|string|max:255",
            'lastname' => "nullable|string|max:255",
            'fullname' => "nullable|string|max:255",

            'role_ids' => "array|nullable",
            'role_ids.*' => "required|exists:roles,id",

            'roles' => "array|nullable",
            'roles.*' => "required|exists:roles,alias",

            'per_page' => "nullable|numeric|max:100",
        ];
    }
}
