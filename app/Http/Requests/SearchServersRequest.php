<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchServersRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'ram' => ['sometimes', 'string', 'nullable'],
            'location' => ['sometimes', 'string', 'nullable'],
            'storage' => ['sometimes', 'numeric', 'nullable'],
            'hard_disk_type' => ['sometimes', 'string', 'nullable'],
            'page' => ['sometimes', 'numeric'],
            'per_page' => ['sometimes', 'numeric'],
        ];
    }
}
