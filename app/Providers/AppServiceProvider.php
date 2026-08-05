<?php

namespace App\Providers;

use App\Models\Schedule; // Model Schedule/Jadwal
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL; // <-- Tambahan untuk perbaikan HTTPS Ngrok
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // PERBAIKAN TAMPILAN NGROK (Paksa HTTPS agar CSS/Asset terload sempurna)
        if (request()->server('HTTP_X_FORWARDED_PROTO') == 'https' || env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // 1. ADMIN BYPASS (Admin selalu diizinkan melakukan semua aksi)
        Gate::before(function (User $user, string $ability) {
            if ($user->role === 'admin') {
                return true;
            }
        });

        // 2. GATE CATAT PIKET (Khusus Guru Piket Sesuai Hari)
        Gate::define('catat-piket', function (User $user) {
            // Ambil data profil guru terkait
            $teacher = $user->teacher;

            if (!$teacher) {
                return false;
            }

            // Dapatkan nama hari ini dalam Bahasa Indonesia (contoh: "Senin", "Selasa", dll)
            Carbon::setLocale('id');
            $hariIni = Carbon::now()->isoFormat('dddd');

            // Cek apakah guru ini terdaftar di tabel jadwal piket hari ini
            return Schedule::where('teacher_id', $teacher->id)
                ->whereRaw('LOWER(day) = ?', [strtolower($hariIni)])
                ->exists();
        });
    }
}