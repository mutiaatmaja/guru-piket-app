<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'teacher_code'    => ['required', 'string', 'max:50', 'unique:teachers,teacher_code'],
            'name'            => ['required', 'string', 'max:255'],
            'email'           => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'        => ['nullable', 'string', 'min:8'],
            'role'            => ['required', 'in:guru,piket,admin'],
            'nip'             => ['nullable', 'string', 'max:50', 'unique:teachers,nip'],
            'nik'             => ['nullable', 'string', 'max:50', 'unique:teachers,nik'],
            'subject'         => ['nullable', 'string', 'max:100'],
            'gender'          => ['required', 'in:L,P'],
            'phone'           => ['nullable', 'string', 'max:20'],
            'religion'        => ['nullable', 'string', 'max:50'],
            'address'         => ['nullable', 'string'],
            'last_education'  => ['nullable', 'string', 'max:100'],
            'additional_task' => ['nullable', 'string', 'max:100'],
        ];
    }
}