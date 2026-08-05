<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(auth()->user()->role ?? '', ['admin', 'administrator']);
    }

    public function rules(): array
    {
        $studentId = $this->route('student') ? $this->route('student')->id : null;

        return [
            'name'         => ['required', 'string', 'max:255'],
            'nisn'         => ['nullable', 'string', 'max:50', Rule::unique('students', 'nisn')->ignore($studentId)],
            'nis'          => ['nullable', 'string', 'max:50', Rule::unique('students', 'nis')->ignore($studentId)],
            'class'        => ['required', 'string', 'max:50'],
            'gender'       => ['required', 'in:L,P'],
            'birth_place'  => ['nullable', 'string', 'max:100'],
            'birth_date'   => ['nullable', 'date'],
            'religion'     => ['nullable', 'string', 'max:50'],
            'parent_name'  => ['nullable', 'string', 'max:255'],
            'parent_phone' => ['nullable', 'string', 'max:20'],
            'address'      => ['nullable', 'string'],
            'photo'        => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}