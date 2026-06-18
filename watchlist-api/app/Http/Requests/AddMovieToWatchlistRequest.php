<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddMovieToWatchlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'imdb_id' => ['required', 'string', 'regex:/^tt\d+$/i'],
            'status' => ['required', 'in:pending,watched,skipped'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
