<?php

namespace App\Http\Requests\Role;

use App\Models\Permission;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;

class ManageRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('role_manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "role_id" => "required|integer|exists:roles,id",
            "permission_ids" => "required|nullable",
            "permission_ids.*" => "integer|exists:permissions,id",
            "action" => "required|string|in:" . implode(",", Permission::MANAGE_ACTIONS),
        ];
    }
}
