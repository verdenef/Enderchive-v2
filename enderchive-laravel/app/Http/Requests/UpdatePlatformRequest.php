<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlatformRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $platformId = $this->route('platform')->platform_id ?? null;
        
        return [
            'name' => 'sometimes|required|string|max:255|unique:platforms,name,' . $platformId . ',platform_id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Platform name is required',
            'name.unique' => 'Platform name already exists',
        ];
    }
}