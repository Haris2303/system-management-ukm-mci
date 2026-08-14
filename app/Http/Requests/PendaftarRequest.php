<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class PendaftarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $tahunSekarang = (int) date('Y');
        $tahunTerlama  = $tahunSekarang - 3;

        $rules = [
            'nama'      => ['required', 'string', 'max:100'],
            'nim'       => ['required', 'string', 'max:20', 'digits:12', 'unique:pendaftars,nim'],
            'email'     => ['required', 'email:rfc,dns', 'max:255', 'unique:pendaftars,email'],
            'no_hp'     => ['required', 'string', 'max:20', 'digits_between:11,13'],
            'angkatan'  => ['required', 'integer', "between:$tahunTerlama,$tahunSekarang"],
            'divisi_id' => ['required', 'exists:divisis,id'],
            'jawaban'   => ['nullable', 'array'],
            'jawaban.*' => ['required', 'string', 'max:2000'],
        ];

        if ($this->routeIs('daftar.validate')) {
            $rules['divisi_id'] = ['nullable', 'exists:divisis,id'];
            // Saat AJAX Step 1 & 2, hiraukan jawaban agar form tidak error duluan
            unset($rules['jawaban'], $rules['jawaban.*']);
        }

        return $rules;
    }

    #[Override]
    public function messages()
    {
        $tahunSekarang = (int) date('Y');
        $tahunTerlama  = $tahunSekarang - 3;

        return [
            'nama.required'         => 'Nama lengkap wajib diisi.',
            'nim.required'          => 'NIM wajib diisi.',
            'nim.digits'            => 'NIM harus tepat 12 digit angka.',
            'nim.unique'           => 'NIM ini sudah terdaftar sebelumnya.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Domain email tidak terdaftar atau tidak valid.',
            'email.unique'         => 'Email ini sudah terdaftar, gunakan email lain.',
            'no_hp.required'        => 'Nomor HP wajib diisi.',
            'no_hp.digits_between'  => 'Nomor HP harus berdurasi antara 11 sampai 13 digit.',
            'angkatan.required'     => 'Angkatan wajib dipilih.',
            'angkatan.between'      => 'Angkatan yang dipilih tidak valid (harus antara ' . $tahunTerlama . ' - ' . $tahunSekarang . ').',
            'divisi_id.required'    => 'Pilih divisi yang ingin Anda masuki.',
            'divisi_id.exists'      => 'Divisi yang dipilih tidak valid.',
            'jawaban.*.required'   => 'Pertanyaan seleksi wajib dijawab.',
        ];
    }
}
