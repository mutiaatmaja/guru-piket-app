<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToModel, WithHeadingRow
{
    // Generate hash password SEKALI SAJA di properti kelas (Bcrypt cost default)
    // Ini menghemat 99% waktu komputasi saat import puluhan/ratusan data
    private string $defaultPasswordHash;

    public function __construct()
    {
        // Password default misalnya: password123
        $this->defaultPasswordHash = Hash::make('password123');
    }

    public function model(array $row)
    {
        // Abaikan baris jika teacher_code atau nama kosong
        if (empty($row['teacher_code']) || empty($row['name'])) {
            return null;
        }

        return DB::transaction(function () use ($row) {
            $teacherCode = trim($row['teacher_code']);
            $email = trim($row['email'] ?? strtolower($teacherCode) . '_guru@smkn7ptk.sch.id');
            $name = trim($row['name']);

            // 1. TIMPA / UPDATE USER (atau buat baru jika belum ada)
            $user = User::where('email', $email)->first();

            if ($user) {
                // Update data user yang sudah ada TANPA mengubah/re-hash password lagi
                $user->update([
                    'name' => $name,
                    'role' => $row['role'] ?? $user->role ?? 'guru',
                ]);
            } else {
                // Buat user baru dengan password default yang sudah di-hash
                $user = User::create([
                    'name'     => $name,
                    'email'    => $email,
                    'password' => $this->defaultPasswordHash,
                    'role'     => $row['role'] ?? 'guru',
                ]);
            }

            // 2. TIMPA / UPDATE DATA GURU (Berdasarkan teacher_code)
            return Teacher::updateOrCreate(
                [
                    'teacher_code' => $teacherCode, // Key unik pencarian
                ],
                [
                    'user_id'         => $user->id,
                    'name'            => $name,
                    'nip'             => $row['nip'] ?? null,
                    'nik'             => $row['nik'] ?? null,
                    'gender'          => $row['gender'] ?? $row['jk'] ?? null,
                    'religion'        => $row['religion'] ?? $row['agama'] ?? null,
                    'subject'         => $row['subject'] ?? $row['mapel'] ?? null,
                    'last_education'  => $row['last_education'] ?? $row['pendidikan'] ?? null,
                    'additional_task' => $row['additional_task'] ?? $row['tugas_tambahan'] ?? null,
                    'phone'           => $row['phone'] ?? $row['no_hp'] ?? null,
                    'address'         => $row['address'] ?? $row['alamat'] ?? null,
                ]
            );
        });
    }
}