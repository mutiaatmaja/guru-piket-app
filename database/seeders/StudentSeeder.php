<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            ['nisn' => '0012345678', 'name' => 'Ahmad Rizky', 'class' => 'XII RPL A', 'gender' => 'L'],
            ['nisn' => '0012345679', 'name' => 'Siti Nurhaliza', 'class' => 'XII RPL A', 'gender' => 'P'],
            ['nisn' => '0012345680', 'name' => 'Budi Santoso', 'class' => 'XI TKJ A', 'gender' => 'L'],
            ['nisn' => '0012345681', 'name' => 'Dewi Lestari', 'class' => 'X AKL B', 'gender' => 'P'],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}