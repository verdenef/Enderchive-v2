<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all authenticated users to create games
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:games,title',
            'description' => 'nullable|string',
            'genre_id' => 'required|exists:genres,genre_id',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'release_date' => 'nullable|date|before_or_equal:today',
            'platform_ids' => 'nullable|array',
            'platform_ids.*' => 'exists:platforms,platform_id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:tags,tag_id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Game title is required',
            'title.unique' => 'A game with this title already exists',
            'genre_id.required' => 'Genre selection is required',
            'genre_id.exists' => 'Selected genre does not exist',
            'platform_ids.*.exists' => 'One or more selected platforms do not exist',
            'tag_ids.*.exists' => 'One or more selected tags do not exist',
            'release_date.before_or_equal' => 'Release date cannot be in the future',
        ];
    }
}
