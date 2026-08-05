<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\Student\StoreStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use App\Http\Requests\Student\ImportStudentRequest;
use App\Imports\StudentsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    /**
     * Tampilkan daftar siswa dengan filter pencarian dan pilihan kelas.
     */
    public function index(Request $request)
    {
        $query = Student::query();

        // 1. Filter Berdasarkan Kelas jika dipilih
        if ($request->filled('class')) {
            $query->where('class', $request->class);
        }

        // 2. Filter Pencarian (Nama, NISN, NIS)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // 3. Ambil daftar kelas unik untuk dropdown filter
        $classList = Student::select('class')
            ->distinct()
            ->whereNotNull('class')
            ->orderBy('class', 'asc')
            ->pluck('class');

        // 4. Pagination Data Siswa
        $students = $query->orderBy('class', 'asc')
                          ->orderBy('name', 'asc')
                          ->paginate(15)
                          ->withQueryString();

        return view('students.index', compact('students', 'classList'));
    }

    /**
     * Simpan data siswa baru beserta upload foto.
     */
    public function store(StoreStudentRequest $request)
    {
        DB::transaction(function () use ($request) {
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('students', 'public');
            }

            Student::create([
                'name'         => $request->name,
                'nisn'         => $request->nisn,
                'nis'          => $request->nis,
                'class'        => $request->class,
                'gender'       => $request->gender,
                'birth_place'  => $request->birth_place,
                'birth_date'   => $request->birth_date,
                'religion'     => $request->religion,
                'parent_name'  => $request->parent_name,
                'parent_phone' => $request->parent_phone,
                'address'      => $request->address,
                'photo'        => $photoPath,
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Update data siswa.
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        DB::transaction(function () use ($request, $student) {
            $photoPath = $student->photo;

            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada
                if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                    Storage::disk('public')->delete($student->photo);
                }
                $photoPath = $request->file('photo')->store('students', 'public');
            }

            $student->update([
                'name'         => $request->name,
                'nisn'         => $request->nisn,
                'nis'          => $request->nis,
                'class'        => $request->class,
                'gender'       => $request->gender,
                'birth_place'  => $request->birth_place,
                'birth_date'   => $request->birth_date,
                'religion'     => $request->religion,
                'parent_name'  => $request->parent_name,
                'parent_phone' => $request->parent_phone,
                'address'      => $request->address,
                'photo'        => $photoPath,
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Hapus data siswa beserta foto profilnya.
     */
    public function destroy(Student $student)
    {
        DB::transaction(function () use ($student) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $student->delete();
        });

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Import Data Siswa via Excel / CSV.
     */
    public function import(ImportStudentRequest $request)
    {
        try {
            Excel::import(new StudentsImport, $request->file('file'));
            return redirect()->route('students.index')->with('success', 'Import data siswa berhasil.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}