<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class ImportTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File Excel/CSV wajib diunggah.',
            'file.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.',
            'file.max'      => 'Ukuran file tidak boleh lebih dari 5MB.',
        ];
    }
}