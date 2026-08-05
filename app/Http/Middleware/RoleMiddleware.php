<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\PiketSchedule;
use App\Models\Teacher;
use Carbon\Carbon;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan pengguna sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // 2. Jika role user cocok dengan role yang diizinkan di route, loloskan langsung
        $userRole = strtolower($user->role ?? '');
        $allowedRoles = array_map('strtolower', $roles);

        if (in_array($userRole, $allowedRoles)) {
            return $next($request);
        }

        // 3. Jika role tidak cocok, cek apakah user bertugas piket HARI INI
        Carbon::setLocale('id');
        $hariIndo = strtolower(trim(Carbon::now()->translatedFormat('l'))); // "kamis"
        $hariEng  = strtolower(trim(Carbon::now()->format('l')));           // "thursday"

        // Ambil data profil guru dari relasi atau pencarian manual
        $teacher = $user->teacher ?? Teacher::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        $teacherCode = $user->teacher_code ?? $teacher?->teacher_code;
        $teacherId   = $teacher?->id;

        // Pengecekan jadwal piket HANYA menggunakan kolom 'day_name'
        $isPiketHariIni = PiketSchedule::where(function ($q) use ($user, $teacherId, $teacherCode) {
                if ($teacherCode) {
                    $q->where('teacher_code', $teacherCode);
                }
                if ($teacherId) {
                    $q->orWhere('teacher_id', $teacherId);
                }
                $q->orWhere('user_id', $user->id);
            })
            ->where(function ($q) use ($hariIndo, $hariEng) {
                $q->whereRaw('LOWER(TRIM(day_name)) LIKE ?', ["%{$hariIndo}%"])
                  ->orWhereRaw('LOWER(TRIM(day_name)) LIKE ?', ["%{$hariEng}%"]);
            })
            ->exists();

        // 4. Jika bertugas piket hari ini, izinkan akses!
        if ($isPiketHariIni) {
            return $next($request);
        }

        // 5. Jika bukan role yang diizinkan dan BUKAN petugas piket hari ini, tolak akses
        return redirect()->route('dashboard')->with(
            'error',
            'Akses ditolak. Anda tidak memiliki izin atau sedang tidak bertugas piket hari ini.'
        );
    }
}