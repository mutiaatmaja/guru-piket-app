<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Http\Requests\Teacher\StoreTeacherRequest;
use App\Http\Requests\Teacher\UpdateTeacherRequest;
use App\Http\Requests\Teacher\ImportTeacherRequest;
use App\Imports\TeachersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TeacherController extends Controller
{
    /**
     * Tampilkan daftar guru dengan fitur pencarian.
     */
    public function index(Request $request)
    {
        $query = Teacher::with('user');

        // Filter Pencarian (Nama, NIP, Mapel, Alamat)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $teachers = $query->latest()->paginate(12)->withQueryString();

        return view('teachers.index', compact('teachers'));
    }

    /**
     * Simpan data guru baru + Akun User Login + Photo Profile.
     */
    public function store(StoreTeacherRequest $request)
    {
        DB::transaction(function () use ($request) {
            // 1. Buat Akun User
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password ?? 'password123'),
                'role'     => $request->role ?? 'guru',
            ]);

            // 2. Handle Upload Foto
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('teachers', 'public');
            }

            // 3. Buat Record Profil Teacher
            Teacher::create([
                'user_id'         => $user->id,
                'teacher_code'    => $request->teacher_code ?? ('T-' . time()),
                'name'            => $request->name,
                'nip'             => $request->nip,
                'nik'             => $request->nik,
                'gender'          => $request->gender,
                'religion'        => $request->religion,
                'subject'         => $request->subject,
                'last_education'  => $request->last_education,
                'additional_task' => $request->additional_task,
                'phone'           => $request->phone,
                'address'         => $request->address,
                'photo'           => $photoPath,
            ]);
        });

        return redirect()->route('teachers.index')->with('success', 'Data Guru berhasil ditambahkan.');
    }

    /**
     * Update data guru (bisa dilakukan Admin atau Guru pemilik akun).
     */
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        // Pengecekan Otorisasi (Hanya Admin atau Pemilik Akun)
        $isSelf = auth()->check() && (
            auth()->id() === $teacher->user_id || 
            auth()->user()->email === ($teacher->user->email ?? '')
        );

        if (auth()->user()->role !== 'admin' && !$isSelf) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah data guru ini.');
        }

        DB::transaction(function () use ($request, $teacher) {
            // 1. Update Data User Login
            if ($teacher->user) {
                $userData = [
                    'name'  => $request->name,
                    'email' => $request->email,
                ];

                // Role HANYA BISA DIUBAH OLEH ADMIN
                if (auth()->user()->role === 'admin' && $request->filled('role')) {
                    $userData['role'] = $request->role;
                }

                // Update Password jika diisi
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                $teacher->user->update($userData);
            }

            // 2. Handle Update Foto Profil
            $photoPath = $teacher->photo;
            if ($request->hasFile('photo')) {
                // Hapus foto lama jika ada
                if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                    Storage::disk('public')->delete($teacher->photo);
                }
                $photoPath = $request->file('photo')->store('teachers', 'public');
            }

            // 3. Update Record Profil Teacher
            $teacher->update([
                'name'            => $request->name,
                'nip'             => $request->nip,
                'nik'             => $request->nik,
                'gender'          => $request->gender,
                'religion'        => $request->religion,
                'subject'         => $request->subject,
                'last_education'  => $request->last_education,
                'additional_task' => $request->additional_task,
                'phone'           => $request->phone,
                'address'         => $request->address,
                'photo'           => $photoPath,
            ]);
        });

        return redirect()->route('teachers.index')->with('success', 'Data Guru berhasil diperbarui.');
    }

    /**
     * Hapus data guru dan foto profilnya (Hanya Admin).
     */
    public function destroy(Teacher $teacher)
    {
        // Proteksi agar user tidak menghapus akun pribadinya sendiri
        if (auth()->id() === $teacher->user_id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($teacher) {
            // Hapus Foto dari Storage
            if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                Storage::disk('public')->delete($teacher->photo);
            }

            // Hapus Akun User
            if ($teacher->user) {
                $teacher->user->delete();
            }

            // Hapus Profil Teacher
            $teacher->delete();
        });

        return redirect()->route('teachers.index')->with('success', 'Data Guru berhasil dihapus.');
    }

    /**
     * Import Data Guru dari File Excel.
     */
    public function import(ImportTeacherRequest $request)
    {
        try {
            Excel::import(new TeachersImport, $request->file('file'));
            return redirect()->route('teachers.index')->with('success', 'Import Data Guru berhasil.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }
}