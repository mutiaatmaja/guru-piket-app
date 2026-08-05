<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class ImportStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role ?? '', ['admin', 'administrator']);
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ];
    }
}