<?php

use Illuminate\Support\Facades\Route;
use App\Models\Attendance;
use App\Models\PiketSchedule;
use App\Models\Schedule;
use App\Models\TeacherJournal;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PiketController;
use App\Http\Controllers\PiketScheduleController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\JournalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ------------------------------------------------------------------------
// Halaman Depan
// ------------------------------------------------------------------------
Route::get('/', function () {
    return redirect()->route('login');
});

// ------------------------------------------------------------------------
// Group Utama: Wajib Login (Authenticated)
// ------------------------------------------------------------------------
Route::middleware(['auth'])->group(function () {

    // 1. Dashboard Ringkasan Piket & Jadwal
    Route::get('/dashboard', function () {
        $today = now()->toDateString();

        // Format nama hari (Bahasa Indonesia & Inggris)
        $dayIndo = now()->locale('id')->isoFormat('dddd');
        $dayEng  = now()->format('l');

        // Mengambil jadwal guru piket hari ini
        $todaySchedules = PiketSchedule::with(['teacher'])
            ->whereIn('day_name', [
                strtolower($dayIndo), ucfirst($dayIndo),
                strtolower($dayEng),  ucfirst($dayEng)
            ])
            ->get();

        // Mengambil seluruh data catatan piket hari ini
        $todayAttendances = Attendance::with('recorder')
            ->whereDate('date', $today)
            ->latest()
            ->get();

        // Statistik Piket Siswa Hari Ini
        $studentStats = [
            'terlambat' => Attendance::whereDate('date', $today)->where('type', 'siswa')->where('status', 'terlambat')->count(),
            'izin'      => Attendance::whereDate('date', $today)->where('type', 'siswa')->where('status', 'izin')->count(),
            'sakit'     => Attendance::whereDate('date', $today)->where('type', 'siswa')->where('status', 'sakit')->count(),
            'alpa'      => Attendance::whereDate('date', $today)->where('type', 'siswa')->where('status', 'alpa')->count(),
        ];

        // Statistik Piket Guru Hari Ini
        $teacherStats = [
            'terlambat' => Attendance::whereDate('date', $today)->where('type', 'guru')->where('status', 'terlambat')->count(),
            'izin'      => Attendance::whereDate('date', $today)->where('type', 'guru')->where('status', 'izin')->count(),
            'sakit'     => Attendance::whereDate('date', $today)->where('type', 'guru')->where('status', 'sakit')->count(),
            'alpa'      => Attendance::whereDate('date', $today)->where('type', 'guru')->where('status', 'alpa')->count(),
        ];

        // 5 Catatan Piket Terbaru Hari Ini
        $recentAttendances = $todayAttendances->take(5);

        // Data Jadwal Pelajaran Hari Ini & Jurnal Terbaru
        $todayLessonSchedules = Schedule::with(['subject', 'teacher'])
            ->whereIn('day', [
                strtolower($dayIndo), ucfirst($dayIndo),
                strtolower($dayEng),  ucfirst($dayEng)
            ])
            ->orderBy('time_slot')
            ->get();

        $recentJournals = TeacherJournal::with(['teacher', 'subject'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'studentStats', 
            'teacherStats', 
            'recentAttendances', 
            'todayAttendances',
            'todaySchedules',
            'todayLessonSchedules',
            'recentJournals'
        ));
    })->name('dashboard');

    // 2. Profile User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 3. View Master Data (Siswa & Guru)
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');

    // 4. Jadwal Piket (View)
    Route::get('/piket-schedules', [PiketScheduleController::class, 'index'])->name('piket-schedules.index');

    // Management Jadwal Piket (Admin & Wakasek)
    Route::middleware(['role:admin,administrator,wakasek'])->group(function () {
        Route::post('/piket-schedules', [PiketScheduleController::class, 'store'])->name('piket-schedules.store');
        
        // Handling GET & POST Import Excel Jadwal Piket
        Route::get('/piket-schedules/import', function () {
            return redirect()->route('piket-schedules.index');
        });
        Route::post('/piket-schedules/import', [PiketScheduleController::class, 'import'])->name('piket-schedules.import');

        Route::put('/piket-schedules/{piketSchedule}', [PiketScheduleController::class, 'update'])->name('piket-schedules.update');
        Route::delete('/piket-schedules/{piketSchedule}', [PiketScheduleController::class, 'destroy'])->name('piket-schedules.destroy');
    });

    // 5. Pencatatan Piket Individu & KBM Kelas (Admin, Guru Piket, Wakasek)
    Route::middleware(['role:admin,administrator,guru_piket,wakasek,piket'])->group(function () {
        Route::get('/piket/create', [PiketController::class, 'create'])->name('piket.create');
        Route::post('/piket', [PiketController::class, 'store'])->name('piket.store');

        Route::get('/piket/kbm', [PiketController::class, 'indexKbm'])->name('piket.kbm');
        Route::post('/piket/kbm', [PiketController::class, 'storeKbm'])->name('piket.kbm.store');

        // Alias Route Attendance
        Route::get('/attendance', [PiketController::class, 'create'])->name('attendance.index');
        Route::post('/attendance', [PiketController::class, 'store'])->name('attendance.store');
    });

    // Edit & Hapus Catatan Piket (Admin & Wakasek)
    Route::middleware(['role:admin,administrator,wakasek'])->group(function () {
        Route::get('/piket/{id}/edit', [PiketController::class, 'edit'])->name('piket.edit');
        Route::put('/piket/{id}', [PiketController::class, 'update'])->name('piket.update');
        Route::delete('/piket/{id}', [PiketController::class, 'destroy'])->name('piket.destroy');
    });

    // 6. Laporan Piket, Print, & Cetak Dokumen Tambahan
    Route::middleware(['role:kepala_sekolah,wakasek,guru_piket,guru,admin,administrator,piket'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        
        // Central Download / Doc Dokumen (Mendukung path /doc/{type} dan /download/{type})
        Route::get('/reports/doc/{type}', [ReportController::class, 'downloadDoc'])->name('reports.download-doc');
        Route::get('/reports/download/{type}', [ReportController::class, 'downloadDoc']);

        // Direct Access Download
        Route::get('/reports/download/daftar-hadir', [ReportController::class, 'downloadDaftarHadirBulanan'])->name('reports.download.daftar-hadir');
        Route::get('/reports/download/rekap-presensi', [ReportController::class, 'downloadRekapPresensi'])->name('reports.download.rekap-presensi');
        Route::get('/reports/download/blanko-nilai', [ReportController::class, 'downloadBlankoNilai'])->name('reports.download.blanko-nilai');
    });

    // 7. Jadwal Pelajaran (Schedules)
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');

    // Management Jadwal Pelajaran (Admin, Administrator, Wakasek)
    Route::middleware(['role:admin,administrator,wakasek'])->group(function () {
        Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
        Route::get('/schedules/export', [ScheduleController::class, 'export'])->name('schedules.export');
        Route::post('/schedules/import', [ScheduleController::class, 'import'])->name('schedules.import');
        Route::put('/schedules/{id}', [ScheduleController::class, 'update'])->name('schedules.update');
        Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
    });

    // 8. Jurnal Mengajar Guru (Teacher Journals)
    Route::middleware(['role:admin,administrator,guru,guru_piket,wakasek,piket'])->group(function () {
        Route::get('/journals', [JournalController::class, 'index'])->name('journals.index');
        Route::post('/journals', [JournalController::class, 'store'])->name('journals.store');
        
        // Cetak PDF Jurnal
        Route::get('/journals/print-pdf', [JournalController::class, 'printPdf'])->name('journals.printPdf');
        Route::get('/journals/print-pdf-alt', [JournalController::class, 'printPdf'])->name('journals.print-pdf');
    });

    // 9. Master Data Management (CRUD & Import Guru & Siswa - Admin/Administrator/Wakasek)
    Route::middleware(['role:admin,administrator,wakasek'])->group(function () {
        
        Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

        // Management Master Guru (Termasuk Otomatisasi Akun User & Import)
        Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
        Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import');

        // Management Master Siswa (CRUD & Import Excel dengan No HP)
        Route::post('/students', [StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
        Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    });

});

require __DIR__.'/auth.php';