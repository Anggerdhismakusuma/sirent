<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'body' => ['required_without:attachment', 'nullable', 'string', 'max:1000'],
            'attachment' => ['required_without:body', 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'body.required_without' => 'Pesan atau lampiran wajib diisi.',
            'body.max' => 'Pesan maksimal 1000 karakter.',
            'attachment.required_without' => 'Pesan atau lampiran wajib diisi.',
            'attachment.image' => 'Lampiran harus berupa gambar.',
            'attachment.mimes' => 'Lampiran harus berformat jpg, jpeg, atau png.',
            'attachment.max' => 'Ukuran lampiran maksimal 2MB.',
        ];
    }
}
