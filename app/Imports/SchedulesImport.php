<?php

namespace App\Imports;

use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SchedulesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {
            $teacherCode = trim($row['teacher_code'] ?? '');

            // 1. Cari Guru berdasarkan teacher_code
            $teacher = Teacher::where('teacher_code', $teacherCode)->first();
            if (!$teacher) {
                return null;
            }

            // 2. Cari atau Buat Mata Pelajaran (Subject)
            $subjectName = trim($row['subject_name'] ?? $row['subject'] ?? $row['mapel'] ?? 'Umum');
            
            // Cek apakah subject sudah ada berdasarkan nama
            $subject = Subject::where('name', $subjectName)->first();

            if (!$subject) {
                // Jika belum ada, buat kode otomatis (misal: "Upacara Bendera" -> "UPACARA-BEN")
                $generatedCode = strtoupper(Str::slug(Str::limit($subjectName, 10, ''), '-'));
                
                // Cegah duplicate code jika ada bentrok
                $count = Subject::where('code', 'LIKE', "{$generatedCode}%")->count();
                if ($count > 0) {
                    $generatedCode .= '-' . ($count + 1);
                }

                $subject = Subject::create([
                    'name' => $subjectName,
                    'code' => $generatedCode, // Mengisi kolom code agar tidak gagal 'NOT NULL'
                ]);
            }

            // 3. Normalisasi Hari, Kelas & Jam Slot
            $day = ucfirst(strtolower(trim($row['day'] ?? $row['hari'] ?? 'Senin')));
            $className = trim($row['class_name'] ?? $row['class'] ?? $row['kelas'] ?? '');
            $timeSlot = (int) ($row['time_slot'] ?? $row['jam_ke'] ?? 1);

            // 4. SIMPAN / UPDATE JADWAL
            return Schedule::updateOrCreate(
                [
                    'day'        => $day,
                    'class_name' => $className,
                    'time_slot'  => $timeSlot,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                ]
            );
        });
    }

    public function rules(): array
    {
        return [
            'teacher_code' => ['required', 'string', 'exists:teachers,teacher_code'],
            'day'          => ['required', 'string'],
            'time_slot'    => ['required'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'teacher_code.required' => 'Kode guru (teacher_code) wajib diisi.',
            'teacher_code.exists'   => 'Kode guru :input tidak ditemukan pada database guru.',
            'day.required'          => 'Hari wajib diisi.',
            'time_slot.required'    => 'Jam ke (time_slot) wajib diisi.',
        ];
    }
}