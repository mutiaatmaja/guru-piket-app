<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PiketSchedule; // Pastikan nama model ini sesuai di project Anda
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CheckPiketSchedule
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Bypass untuk Role Admin / Waka / Administrator
        $role = strtolower($user->role ?? '');
        if (in_array($role, ['admin', 'waka', 'administrator', 'waka kurikulum'])) {
            return $next($request);
        }

        $teacher = $user->teacher ?? null;

        // 2. Ambil Variasi Nama Hari Ini
        Carbon::setLocale('id');
        $hariIndo = strtolower(trim(Carbon::now()->translatedFormat('l'))); // "kamis"
        $hariEng  = strtolower(trim(Carbon::now()->format('l')));           // "thursday"

        // 3. Pengecekan Kombinasi
        $isScheduled = PiketSchedule::where(function ($q) use ($user, $teacher) {
                if ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                }
                $q->orWhere('user_id', $user->id);
            })
            ->where(function ($q) use ($hariIndo, $hariEng) {
                $q->whereRaw('LOWER(TRIM(day)) LIKE ?', ["%{$hariIndo}%"])
                  ->orWhereRaw('LOWER(TRIM(day)) LIKE ?', ["%{$hariEng}%"]);
            })
            ->exists();

        // --- DEBUGGING (Letak dd() yang benar) ---
        // Hapus atau beri komentar pada baris dd() di bawah jika masalah sudah selesai
        dd([
            'User ID' => $user->id,
            'Teacher ID' => $teacher->id ?? 'Tidak ditemukan relasi teacher',
            'Hari Indo' => $hariIndo,
            'Hari Eng' => $hariEng,
            'Is Scheduled Result' => $isScheduled,
            'Data Piket DB' => PiketSchedule::all()->toArray()
        ]);

        if (!$isScheduled) {
            $namaHari = Carbon::now()->translatedFormat('l');
            return redirect()->route('dashboard')->with(
                'error', 
                "Akses Ditolak! Anda tidak terdaftar sebagai Petugas Piket hari {$namaHari}."
            );
        }

        return $next($request);
    }
}