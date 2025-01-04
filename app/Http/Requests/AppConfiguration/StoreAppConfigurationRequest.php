<?php

namespace App\Http\Requests\AppConfiguration;

use App\Models\AppConfiguration;
use App\Traits\Requests\Requestable;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppConfigurationRequest extends FormRequest
{
    use Requestable;

    use Requestable;

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
            'code' => ["required", "string", "max:255"],
            'name' => ["required", "string", "max:255"],
            'value' => ["required"],
            'type' => 'required|in:' . implode(',', AppConfiguration::TYPES),
            'visible' => ["nullable", "boolean"],
            'description' => ["nullable", "string"],
        ];
    }
}
