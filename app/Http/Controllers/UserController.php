<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserController extends Controller
{
    /**
     * Tampilkan Daftar User beserta relasi Teacher
     */
    public function index()
    {
        $users = User::with('teacher')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Tambah User Manual + Otomatis Buat Data Teacher
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,administrator,guru_piket,guru,wakasek,kepala_sekolah',
            'nip'      => 'nullable|string|unique:teachers,nip',
            'subject'  => 'nullable|string',
            'phone'    => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat User
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            // 2. Otomatis buat data Teacher yang terhubung ke user_id
            Teacher::create([
                'user_id' => $user->id,
                'name'    => $user->name,
                'nip'     => $request->nip,
                'subject' => $request->subject ?? '-',
                'phone'   => $request->phone,
            ]);
        });

        return back()->with('success', 'User dan Data Guru berhasil ditambahkan!');
    }

    /**
     * Update User + Otomatis Update Data Teacher Terkait
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'role'     => 'required|in:admin,administrator,guru_piket,guru,wakasek,kepala_sekolah',
            'password' => 'nullable|string|min:8',
            'nip'      => 'nullable|string|unique:teachers,nip,' . ($user->teacher->id ?? 0),
            'subject'  => 'nullable|string',
            'phone'    => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $user) {
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
                'role'  => $request->role,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            if ($user->teacher) {
                $user->teacher->update([
                    'name'    => $request->name,
                    'nip'     => $request->nip,
                    'subject' => $request->subject ?? $user->teacher->subject,
                    'phone'   => $request->phone,
                ]);
            }
        });

        return back()->with('success', 'Data User berhasil diperbarui!');
    }

    /**
     * Hapus User + Otomatis Hapus Profile Teacher Terkait
     */
    public function destroy(User $user)
    {
        $authUser = auth()->user();

        if ($authUser->id === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        DB::transaction(function () use ($user) {
            if ($user->teacher) {
                if ($user->teacher->photo && Storage::disk('public')->exists($user->teacher->photo)) {
                    Storage::disk('public')->delete($user->teacher->photo);
                }
                $user->teacher->delete();
            }

            $user->delete();
        });

        return back()->with('success', 'User beserta data profil guru berhasil dihapus!');
    }

    /**
     * Import File Excel (.xlsx / .xls) & CSV + Otomatis Buat Data Teacher
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:5120',
        ], [
            'file.mimes' => 'Format berkas harus berupa .xlsx, .xls, atau .csv',
        ]);

        try {
            $file = $request->file('file');

            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File Excel/CSV kosong atau tidak berisi data.');
            }

            $rawHeader = array_shift($rows);
            $header = [];

            foreach ($rawHeader as $key => $value) {
                $clean = strtolower(trim(preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/s', '', (string)$value)));

                if (in_array($clean, ['e-mail', 'mail', 'email_guru'])) $clean = 'email';
                if (in_array($clean, ['nama_lengkap', 'nama_guru', 'name'])) $clean = 'name';
                if (in_array($clean, ['no_telepon', 'nohp', 'phone', 'telepon'])) $clean = 'phone';
                if (in_array($clean, ['subject', 'mata_pelajaran', 'mapel'])) $clean = 'subject';

                if (!empty($clean)) {
                    $header[$key] = $clean;
                }
            }

            $importedCount = 0;
            $skippedCount = 0;
            $allowedRoles = ['admin', 'administrator', 'guru_piket', 'guru', 'wakasek', 'kepala_sekolah'];

            foreach ($rows as $data) {
                $row = [];
                foreach ($header as $colKey => $headerName) {
                    $row[$headerName] = isset($data[$colKey]) ? trim((string)$data[$colKey]) : '';
                }

                if (array_filter($row) === []) {
                    continue;
                }

                $email = $row['email'] ?? '';
                if (empty($email) || User::where('email', $email)->exists()) {
                    $skippedCount++;
                    continue;
                }

                $role = strtolower($row['role'] ?? 'guru');
                if (!in_array($role, $allowedRoles)) {
                    $role = 'guru';
                }

                DB::transaction(function () use ($row, $role, $email) {
                    $user = User::create([
                        'name'     => !empty($row['name']) ? $row['name'] : 'User Baru',
                        'email'    => $email,
                        'password' => Hash::make(!empty($row['password']) ? $row['password'] : 'password123'),
                        'role'     => $role,
                    ]);

                    Teacher::create([
                        'user_id' => $user->id,
                        'name'    => $user->name,
                        'nip'     => !empty($row['nip']) ? $row['nip'] : null,
                        'subject' => !empty($row['subject']) ? $row['subject'] : '-',
                        'phone'   => !empty($row['phone']) ? $row['phone'] : null,
                    ]);
                });

                $importedCount++;
            }

            if ($importedCount === 0 && $skippedCount > 0) {
                return back()->with('error', "Gagal impor! Terdapat {$skippedCount} baris yang dilewati (email kosong/sudah terdaftar).");
            }

            $message = "Berhasil mengimpor {$importedCount} akun User & data Guru!";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} data dilewati karena email duplikat/kosong)";
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
}