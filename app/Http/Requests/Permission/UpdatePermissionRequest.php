<?php

namespace App\Http\Requests\Permission;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('permission_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "string",
            "is_active" => "boolean",
            "description" => "nullable|string",
            "module" => "nullable|string",
        ];
    }
}
