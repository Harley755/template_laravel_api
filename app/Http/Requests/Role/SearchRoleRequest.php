<?php

namespace App\Http\Requests\Role;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class SearchRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('role_create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string",
            "alias" => "required|string",
            "description" => "nullable|string",
            "permissions" => "array|nullable",
            "permissions.*" => "integer|exists:permissions,id",
        ];
    }
}