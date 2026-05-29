<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PresensiStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'size:32'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token QR Code wajib disertakan.',
            'token.string'   => 'Format token tidak valid.',
            'token.size'     => 'Panjang token tidak sesuai. Pastikan QR Code yang Anda scan benar.',
        ];
    }
}
