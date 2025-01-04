<?php

namespace App\Http\Requests\Permission;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class SearchPermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('permission_search');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "nullable|string",
            "resource" => "nullable|string",
            "action" => "nullable|string",
            "role_id" => "nullable|integer|exists:roles,id",
            "is_active" => "boolean",
            "description" => "nullable|string",
            "module" => "nullable|string",
        ];
    }
}
