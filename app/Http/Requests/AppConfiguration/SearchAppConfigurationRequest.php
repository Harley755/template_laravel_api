<?php

namespace App\Http\Requests\AppConfiguration;

use App\Traits\Requests\Requestable;
use Illuminate\Foundation\Http\FormRequest;

class SearchAppConfigurationRequest extends FormRequest
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
            "code" => ["nullable", "string", "max:255"],
        ];
    }
}
