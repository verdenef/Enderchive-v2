<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tagId = $this->route('tag')->tag_id ?? null;
        
        return [
            'name' => 'sometimes|required|string|max:255|unique:tags,name,' . $tagId . ',tag_id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tag name is required',
            'name.unique' => 'Tag name already exists',
        ];
    }
}