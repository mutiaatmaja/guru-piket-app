<?php

namespace App\Imports;

use App\Models\PiketSchedule;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PiketSchedulesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $teacherCode = trim($row['teacher_code']);

            // 1. Cari guru berdasarkan teacher_code
            $teacher = Teacher::where('teacher_code', $teacherCode)->first();

            if (!$teacher) {
                return null;
            }

            // 2. Normalisasi Nama Hari
            $dayName = ucfirst(strtolower(trim($row['day_name'] ?? $row['day'] ?? '')));

            // 3. Normalisasi Status
            $rawStatus = strtolower(trim($row['status'] ?? 'aktif'));
            $status = in_array($rawStatus, ['aktif', 'active', '1', 'true']) ? 'aktif' : 'nonaktif';

            // 4. Simpan HANYA kolom yang ada di database (tanpa teacher_code)
            return PiketSchedule::updateOrCreate(
                [
                    'teacher_id' => $teacher->id,
                    'day_name'   => $dayName,
                ],
                [
                    'status' => $status,
                    'notes'  => $row['notes'] ?? $row['catatan'] ?? null,
                ]
            );
        });
    }

    public function rules(): array
    {
        return [
            'teacher_code' => ['required', 'string', 'exists:teachers,teacher_code'],
            'day_name'     => ['required', 'string'],
            'status'       => ['nullable', 'string'],
            'notes'        => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'teacher_code.required' => 'Kode guru (teacher_code) wajib diisi.',
            'teacher_code.exists'   => 'Kode guru :input tidak ditemukan pada database guru.',
            'day_name.required'     => 'Nama hari piket wajib diisi.',
        ];
    }
}