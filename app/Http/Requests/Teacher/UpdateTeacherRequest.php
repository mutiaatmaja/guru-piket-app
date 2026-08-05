<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');
        $teacherId = $teacher ? $teacher->id : null;
        $userId    = $teacher ? $teacher->user_id : null;

        return [
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password'        => ['nullable', 'string', 'min:8'],
            'role'            => ['nullable', 'string'],
            'nip'             => ['nullable', 'string', 'max:50', Rule::unique('teachers', 'nip')->ignore($teacherId)],
            'nik'             => ['nullable', 'string', 'max:50', Rule::unique('teachers', 'nik')->ignore($teacherId)],
            'gender'          => ['required', 'in:L,P'],
            'religion'        => ['nullable', 'string', 'max:50'],
            'subject'         => ['required', 'string', 'max:100'],
            'last_education'  => ['nullable', 'string', 'max:100'],
            'additional_task' => ['nullable', 'string', 'max:100'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'address'         => ['nullable', 'string'],
            'photo'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }
}