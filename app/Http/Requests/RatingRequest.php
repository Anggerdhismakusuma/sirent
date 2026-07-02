<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'score'  => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'score.required' => 'Rating bintang wajib diisi.',
            'score.integer'  => 'Rating harus berupa angka.',
            'score.min'      => 'Rating minimal 1 bintang.',
            'score.max'      => 'Rating maksimal 5 bintang.',
            'review.max'     => 'Ulasan maksimal 500 karakter.',
        ];
    }
}
