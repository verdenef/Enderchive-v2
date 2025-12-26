<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow authenticated users to update their games
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'sometimes|required|in:Wishlist,Playing,Completed,Abandoned',
            'rating' => 'nullable|integer|min:1|max:10',
            'playtime_hours' => 'nullable|integer|min:0|max:9999',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: Wishlist, Playing, Completed, Abandoned',
            'rating.integer' => 'Rating must be a number',
            'rating.min' => 'Rating must be at least 1',
            'rating.max' => 'Rating must be at most 10',
            'playtime_hours.integer' => 'Playtime must be a number',
            'playtime_hours.min' => 'Playtime cannot be negative',
            'playtime_hours.max' => 'Playtime cannot exceed 9999 hours',
        ];
    }
}