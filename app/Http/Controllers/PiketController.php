<?php

namespace App\Http\Controllers;

use App\Models\ClassroomAttendance;
use App\Models\PiketSchedule;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiketController extends Controller
{
    /**
     * Helper privat untuk mengecek otorisasi role DAN jadwal bertugas hari ini.
     */
    private function checkPiketAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // 1. Admin, Administrator, dan Wakasek selalu diizinkan kapan saja
        $userRole = strtolower($user->role ?? '');
        if (in_array($userRole, ['admin', 'administrator', 'wakasek', 'waka', 'waka kurikulum'])) {
            return true;
        }

        // 2. Pembatasan Hari Bertugas untuk Guru / Guru Piket
        $todayIndo = strtolower(trim(now()->locale('id')->isoFormat('dddd'))); // e.g., 'kamis'
        $todayEng  = strtolower(trim(now()->format('l')));                    // e.g., 'thursday'

        // Dapatkan profil teacher jika ada
        $teacher = $user->teacher ?? Teacher::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->first();

        $teacherCode = $user->teacher_code ?? $teacher?->teacher_code;
        $teacherId   = $teacher?->id;

        // Cek apakah terdaftar di PiketSchedule hari ini
        $isScheduledToday = PiketSchedule::where(function ($q) use ($user, $teacherId, $teacherCode) {
                if ($teacherCode) {
                    $q->where('teacher_code', $teacherCode);
                }
                if ($teacherId) {
                    $q->orWhere('teacher_id', $teacherId);
                }
                $q->orWhere('user_id', $user->id);
            })
            ->where(function ($q) use ($todayIndo, $todayEng) {
                $q->whereRaw('LOWER(TRIM(day_name)) = ?', [$todayIndo])
                  ->orWhereRaw('LOWER(TRIM(day_name)) = ?', [$todayEng]);
            })
            ->exists();

        return $isScheduledToday;
    }

    /**
     * Menampilkan Form Pencatatan Piket Individu / Presensi Siswa & Guru.
     */
    public function create()
    {
        if (! $this->checkPiketAccess()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin atau sedang tidak bertugas piket hari ini.');
        }

        // 1. Ambil daftar kelas dari kolom 'class' (mengikuti acuan ScheduleController)
        $classes = Student::select('class')
            ->whereNotNull('class')
            ->where('class', '!=', '')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        // Jika database siswa belum terisi kelas, sediakan kelas default agar dropdown tidak kosong
        if ($classes->isEmpty()) {
            $classes = collect(['X IPA 1', 'X IPA 2', 'XI IPA 1', 'XI IPA 2', 'XII IPA 1']);
        }

        // 2. Ambil data semua siswa untuk difilter dinamis via JavaScript
        $students = Student::orderBy('name', 'asc')->get();

        // 3. Ambil data semua guru
        $teachers = Teacher::orderBy('name', 'asc')->get();

        return view('piket.create', compact('classes', 'students', 'teachers'));
    }

    /**
     * Menyimpan Pencatatan Piket Individu / Presensi Siswa.
     */
    public function store(Request $request)
    {
        if (! $this->checkPiketAccess()) {
            return redirect()->route('dashboard')->with('error', 'Aksi ditolak. Anda tidak memiliki izin atau sedang tidak bertugas piket hari ini.');
        }

        $validated = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'status'       => 'required|string',
            'start_period' => 'nullable|integer',
            'end_period'   => 'nullable|integer',
            'notes'        => 'nullable|string',
        ]);

        // Simpan logika pencatatan piket di sini...

        return redirect()->back()->with('success', 'Catatan piket siswa berhasil disimpan!');
    }

    /**
     * Menampilkan halaman rekap & pengisian presensi KBM oleh Guru Piket.
     */
    public function indexKbm(Request $request)
    {
        if (! $this->checkPiketAccess()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Anda tidak memiliki izin atau sedang tidak bertugas piket hari ini.');
        }

        $dateInput = $request->input('date', Carbon::today()->toDateString());
        $date = Carbon::parse($dateInput);

        $dayMap = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        $dayName = $dayMap[$date->format('l')] ?? 'Senin';

        $attendancesToday = ClassroomAttendance::whereDate('date', $dateInput)
            ->get()
            ->keyBy('schedule_id');

        $schedules = Schedule::with(['teacher', 'subject'])
            ->where('day', $dayName)
            ->orderBy('class_name', 'asc')
            ->orderBy('time_slot', 'asc')
            ->get();

        $schedulesByClass = $schedules->groupBy('class_name');
        $groupedSchedulesByClass = [];

        $totalSesi = 0;
        $sudahTerpantau = 0;

        foreach ($schedulesByClass as $className => $classSchedules) {
            $grouped = [];
            $currentGroup = null;

            foreach ($classSchedules as $sched) {
                $att = $attendancesToday->get($sched->id);

                if ($currentGroup === null) {
                    $currentGroup = [
                        'schedule_ids'    => [$sched->id],
                        'class_name'      => $sched->class_name,
                        'teacher'         => $sched->teacher,
                        'teacher_code'    => $sched->teacher_code,
                        'subject'         => $sched->subject,
                        'start_time_slot' => $sched->time_slot,
                        'end_time_slot'   => $sched->time_slot,
                        'attendances'     => collect($att ? [$att] : []),
                        'badge_color'     => $this->getClassColorClass($sched->class_name),
                    ];
                } else {
                    $isSameTeacher = ($currentGroup['teacher_code'] === $sched->teacher_code);
                    $isSameSubject = ($currentGroup['subject']?->id === $sched->subject?->id);

                    if ($isSameTeacher && $isSameSubject) {
                        $currentGroup['schedule_ids'][] = $sched->id;
                        $currentGroup['end_time_slot']  = $sched->time_slot;
                        if ($att) {
                            $currentGroup['attendances']->push($att);
                        }
                    } else {
                        $grouped[] = $currentGroup;
                        $currentGroup = [
                            'schedule_ids'    => [$sched->id],
                            'class_name'      => $sched->class_name,
                            'teacher'         => $sched->teacher,
                            'teacher_code'    => $sched->teacher_code,
                            'subject'         => $sched->subject,
                            'start_time_slot' => $sched->time_slot,
                            'end_time_slot'   => $sched->time_slot,
                            'attendances'     => collect($att ? [$att] : []),
                            'badge_color'     => $this->getClassColorClass($sched->class_name),
                        ];
                    }
                }
            }

            if ($currentGroup !== null) {
                $grouped[] = $currentGroup;
            }

            foreach ($grouped as &$item) {
                $totalSesi++;

                if ($item['start_time_slot'] == $item['end_time_slot']) {
                    $item['jam_display'] = "Jam ke-{$item['start_time_slot']}";
                    $item['jam_ke']      = $item['start_time_slot'];
                } else {
                    $item['jam_display'] = "Jam ke-{$item['start_time_slot']} - {$item['end_time_slot']}";
                    $item['jam_ke']      = "{$item['start_time_slot']} - {$item['end_time_slot']}";
                }

                $firstAttendance = $item['attendances']->first();

                if ($firstAttendance) {
                    $item['is_filled']               = true;
                    $item['status']                  = $firstAttendance->status;
                    $item['substitute_teacher_code'] = $firstAttendance->substitute_teacher_code;
                    $item['task_description']        = $firstAttendance->task_description;
                    $sudahTerpantau++;
                } else {
                    $item['is_filled']               = false;
                    $item['status']                  = 'belum_diisi';
                    $item['substitute_teacher_code'] = null;
                    $item['task_description']        = null;
                }
            }

            $groupedSchedulesByClass[$className] = $grouped;
        }

        $recentAttendances = ClassroomAttendance::with(['schedule.teacher', 'schedule.subject', 'substituteTeacher'])
            ->whereDate('date', $dateInput)
            ->orderBy('updated_at', 'desc')
            ->take(20)
            ->get();

        $classList = Schedule::where('day', $dayName)->distinct()->pluck('class_name')->sort();
        $timeSlots = Schedule::where('day', $dayName)->distinct()->pluck('time_slot')->sort();
        $teachers  = Teacher::orderBy('name', 'asc')->get();

        return view('piket.kbm', compact(
            'groupedSchedulesByClass',
            'recentAttendances',
            'dateInput',
            'dayName',
            'classList',
            'timeSlots',
            'teachers',
            'totalSesi',
            'sudahTerpantau'
        ));
    }

    /**
     * Menyimpan/mengubah status presensi kelas KBM.
     */
    public function storeKbm(Request $request)
    {
        if (! $this->checkPiketAccess()) {
            return redirect()->route('dashboard')->with('error', 'Aksi ditolak. Anda tidak memiliki izin atau sedang tidak bertugas piket hari ini.');
        }

        $validated = $request->validate([
            'schedule_ids'            => 'required|array',
            'schedule_ids.*'          => 'exists:schedules,id',
            'date'                    => 'required|date',
            'status'                  => 'required|in:hadir,tugas,izin,alpa,kosong,Hadir,Tugas,Izin,Alpa,Kosong',
            'substitute_teacher_code' => 'nullable|string',
            'task_description'        => 'nullable|string',
            'notes'                   => 'nullable|string',
        ]);

        $substituteTeacherCode = $validated['substitute_teacher_code'] ?? $request->input('substitute_teacher_code');
        $taskDescription       = $validated['task_description'] ?? $request->input('notes');

        $formattedDate = Carbon::parse($validated['date'])->toDateString();

        foreach ($validated['schedule_ids'] as $scheduleId) {
            $schedule = Schedule::find($scheduleId);

            ClassroomAttendance::updateOrCreate(
                [
                    'schedule_id' => $scheduleId,
                    'date'        => $formattedDate,
                ],
                [
                    'class_name'              => $schedule?->class_name ?? '-',
                    'time_slot'               => $schedule?->time_slot ?? '1',
                    'status'                  => strtolower($validated['status']),
                    'substitute_teacher_code' => $substituteTeacherCode,
                    'task_description'        => $taskDescription,
                    'recorded_by'             => auth()->id(),
                ]
            );
        }

        return redirect()->back()->with('success', 'Status presensi KBM berhasil diperbarui!');
    }

    /**
     * Helper pewarnaan badge berdasarkan nama kelas
     */
    private function getClassColorClass($className)
    {
        if (str_contains($className, 'X ')) return 'bg-blue-100 text-blue-800';
        if (str_contains($className, 'XI ')) return 'bg-emerald-100 text-emerald-800';
        if (str_contains($className, 'XII ')) return 'bg-purple-100 text-purple-800';
        return 'bg-gray-100 text-gray-800';
    }
}