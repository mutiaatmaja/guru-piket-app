<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        // Mengambil data guru dan siswa untuk pilihan form
        $teachers = Teacher::orderBy('name', 'asc')->get();
        $students = Student::orderBy('name', 'asc')->get();

        return view('attendance.index', compact('teachers', 'students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'teacher_code' => 'required|exists:teachers,teacher_code',
            'student_id'   => 'nullable|exists:students,id',
            'status'       => 'required|string',
            'notes'        => 'nullable|string',
        ]);

        Attendance::create($validated);

        return redirect()->route('attendance.index')->with('success', 'Laporan absensi/piket berhasil disimpan!');
    }
}