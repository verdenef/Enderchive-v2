<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGenreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $genreId = $this->route('genre')->genre_id ?? null;
        
        return [
            'name' => 'sometimes|required|string|max:255|unique:genres,name,' . $genreId . ',genre_id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Genre name is required',
            'name.unique' => 'Genre name already exists',
        ];
    }
}