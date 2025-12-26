<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow authenticated users to add games to their library
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'game_id' => 'required|exists:games,game_id',
            'status' => 'nullable|in:Wishlist,Playing,Completed,Abandoned',
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
            'game_id.required' => 'Game selection is required',
            'game_id.exists' => 'Selected game does not exist',
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