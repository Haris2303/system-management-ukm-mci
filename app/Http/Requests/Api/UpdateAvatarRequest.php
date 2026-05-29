<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return match ($this->input('mode')) {
            'photo' => [
                'foto' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
            ],
            'emoji' => [
                'emoji' => ['required', 'string', 'max:10'],
                'bg'    => ['required', 'string', 'regex:/^[0-9a-fA-F]{6}$/'],
            ],
            default => [],
        };
    }
}
