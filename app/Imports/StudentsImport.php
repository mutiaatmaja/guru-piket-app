<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Carbon\Carbon;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    /**
     * Memetakan baris Excel/CSV ke dalam Model Student.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Parsing Jenis Kelamin (opsi input: L, P, Laki-Laki, Perempuan)
        $gender = strtoupper(trim($row['gender'] ?? 'L'));
        if (in_array($gender, ['PEREMPUAN', 'P'])) {
            $gender = 'P';
        } else {
            $gender = 'L';
        }

        // Parsing Tanggal Lahir (Mendukung format tanggal Excel Serial maupun String Y-m-d)
        $birthDate = null;
        if (!empty($row['birth_date'])) {
            try {
                if (is_numeric($row['birth_date'])) {
                    $birthDate = Carbon::instance(ExcelDate::excelToDateTimeObject($row['birth_date']))->format('Y-m-d');
                } else {
                    $birthDate = Carbon::parse($row['birth_date'])->format('Y-m-d');
                }
            } catch (\Exception $e) {
                $birthDate = null;
            }
        }

        return new Student([
            'name'         => $row['name'] ?? $row['nama'] ?? null,
            'class'        => $row['class'] ?? $row['kelas'] ?? null,
            'gender'       => $gender,
            'nisn'         => $row['nisn'] ?? null,
            'nis'          => $row['nis'] ?? null,
            'birth_place'  => $row['birth_place'] ?? $row['tempat_lahir'] ?? null,
            'birth_date'   => $birthDate,
            'religion'     => $row['religion'] ?? $row['agama'] ?? null,
            'parent_name'  => $row['parent_name'] ?? $row['nama_orang_tua'] ?? null,
            'parent_phone' => $row['parent_phone'] ?? $row['no_hp_ortu'] ?? null,
            'address'      => $row['address'] ?? $row['alamat'] ?? null,
        ]);
    }

    /**
     * Rules Validasi per baris pada file Excel.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.name'  => ['required_without:*.nama'],
            '*.nama'  => ['required_without:*.name'],
            '*.class' => ['required_without:*.kelas'],
            '*.kelas' => ['required_without:*.class'],
            '*.nisn'  => ['nullable', 'distinct'],
            '*.nis'   => ['nullable', 'distinct'],
        ];
    }

    /**
     * Custom Attribute Names untuk Pesan Error Validasi.
     *
     * @return array
     */
    public function customValidationAttributes()
    {
        return [
            '*.name'  => 'Nama Siswa',
            '*.nama'  => 'Nama Siswa',
            '*.class' => 'Kelas',
            '*.kelas' => 'Kelas',
        ];
    }
}