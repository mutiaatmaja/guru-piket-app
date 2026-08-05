<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassroomAttendance;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherJournal;
use App\Models\Subject;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Helper khusus untuk mengambil daftar KELAS yang diajar oleh Guru yang sedang Login
     */
    private function getTeacherClasses()
    {
        $user = Auth::user();
        $myClasses = collect();

        if ($user) {
            $userId   = $user->id;
            $userCode = $user->code ?? $user->kode_guru ?? $user->username ?? null;
            $userNip  = $user->nip ?? null;
            $userName = $user->name ?? null;

            // Cari ID Teacher terhubung jika ada model Teacher
            $teacherId = null;
            if (class_exists('\App\Models\Teacher')) {
                $teacherObj = Teacher::where('user_id', $userId)
                    ->orWhere('id', $userId)
                    ->when($userCode, fn($q) => $q->orWhere('code', $userCode)->orWhere('kode_guru', $userCode))
                    ->when($userNip, fn($q) => $q->orWhere('nip', $userNip))
                    ->first();
                if ($teacherObj) {
                    $teacherId = $teacherObj->id;
                }
            }

            // 1. CARI DARI JADWAL (Schedule) GURU
            if (class_exists('\App\Models\Schedule')) {
                $schedules = Schedule::where(function($q) use ($userId, $teacherId, $userCode, $userNip) {
                    $q->where('teacher_id', $userId);
                    if ($teacherId) {
                        $q->orWhere('teacher_id', $teacherId);
                    }
                    if ($userCode) {
                        $q->orWhere('teacher_code', $userCode)->orWhere('kode_guru', $userCode);
                    }
                    if ($userNip) {
                        $q->orWhere('nip', $userNip);
                    }
                })->get();

                $myClasses = $schedules->pluck('class_name')->filter()->unique();
            }

            // 2. FALLBACK A: JIKA JADWAL KOSONG, CARI DARI JURNAL GURU (TeacherJournal)
            if ($myClasses->isEmpty()) {
                $journalsQuery = TeacherJournal::where(function($q) use ($userId, $teacherId, $userName) {
                    $q->where('teacher_id', $userId);
                    if ($teacherId) {
                        $q->orWhere('teacher_id', $teacherId);
                    }
                    if ($userName) {
                        $q->orWhere('teacher_name', $userName);
                    }
                })->get();

                $myClasses = $journalsQuery->pluck('class_name')->filter()->unique();
            }

            // 3. FALLBACK B: JIKA ADA RELASI CLASSROOMS DI USER
            if ($myClasses->isEmpty() && method_exists($user, 'classrooms') && $user->classrooms) {
                $myClasses = $user->classrooms->pluck('name')->filter()->unique();
            }
        }

        $teacherClasses = array_values(array_filter($myClasses->unique()->toArray()));

        // FALLBACK C: JIKA BUKAN GURU / ADMIN / BELUM MEMILIKI JADWAL, TAMPILKAN SEMUA KELAS
        if (empty($teacherClasses)) {
            $classesFromStudents = Student::pluck('class')->filter()->toArray();
            $classesFromKbm      = ClassroomAttendance::pluck('class_name')->filter()->toArray();
            $classesFromJournal  = TeacherJournal::pluck('class_name')->filter()->toArray();

            $teacherClasses = array_values(array_unique(array_merge(
                $classesFromStudents, 
                $classesFromKbm, 
                $classesFromJournal
            )));
        }

        sort($teacherClasses);

        return $teacherClasses;
    }

    /**
     * Menampilkan Halaman Laporan Rekapitulasi Presensi KBM, Jurnal Mengajar, & Catatan Piket
     */
    public function index(Request $request)
    {
        $startDateInput = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDateInput   = $request->input('end_date', now()->toDateString());

        $startDate     = Carbon::parse($startDateInput)->format('Y-m-d');
        $endDate       = Carbon::parse($endDateInput)->format('Y-m-d');
        
        $type          = $request->input('type', 'kbm'); 
        $status        = $request->input('status');
        $selectedClass = $request->input('class_name');

        $journals    = collect();
        $attendances = collect();
        $stats       = [];

        if ($type === 'jurnal') {
            $query = TeacherJournal::with([
                'teacher', 
                'subject', 
                'schedule.teacher', 
                'schedule.subject'
            ])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

            if ($selectedClass && $selectedClass !== '-- Semua Kelas --') {
                $query->where(function($q) use ($selectedClass) {
                    $q->where('class_name', $selectedClass)
                      ->orWhereHas('schedule', function($s) use ($selectedClass) {
                          $s->where('class_name', $selectedClass);
                      });
                });
            }

            $rawJournals = $query->latest('date')->get();

            $journals = $rawJournals->map(function ($j) {
                $j->display_class = $j->class_name 
                    ?? $j->schedule?->class_name 
                    ?? '-';

                $jamStart = $j->formatted_jam_ke ?? $j->time_slot ?? $j->jam_ke ?? $j->schedule?->time_slot ?? $j->schedule?->jam_ke;
                $jamEnd   = $j->time_slot_to ?? $j->schedule?->time_slot_to;

                if ($jamStart && $jamEnd && $jamStart != $jamEnd) {
                    $j->display_jam = "{$jamStart} - {$jamEnd}";
                } elseif ($jamStart) {
                    $j->display_jam = "{$jamStart}";
                } else {
                    $j->display_jam = '-';
                }

                $j->display_teacher = $j->teacher?->name 
                    ?? $j->schedule?->teacher?->name 
                    ?? '-';

                $j->display_subject = $j->subject?->name 
                    ?? $j->subject?->nama_mapel 
                    ?? $j->schedule?->subject?->name 
                    ?? $j->schedule?->subject?->nama_mapel 
                    ?? '-';

                $j->display_materi = $j->material 
                    ?? $j->materi_pokok 
                    ?? $j->materi_pembelajaran 
                    ?? $j->materi 
                    ?? $j->description 
                    ?? '-';

                $j->display_hambatan = $j->notes 
                    ?? $j->hambatan 
                    ?? $j->catatan 
                    ?? $j->keterangan 
                    ?? '-';

                return $j;
            });

            $stats = [
                'total_jurnal' => $journals->count(),
                'total_guru'   => $journals->pluck('teacher_id')->filter()->unique()->count(),
                'total_kelas'  => $journals->pluck('display_class')->filter()->unique()->count(),
            ];

        } elseif ($type === 'kbm') {
            $query = ClassroomAttendance::with([
                'schedule.teacher',
                'schedule.subject',
                'substituteTeacher', 
                'piketUser'
            ])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

            if ($status) {
                $query->where('status', strtolower($status));
            }

            if ($selectedClass && $selectedClass !== '-- Semua Kelas --') {
                $query->where('class_name', $selectedClass);
            }

            $rawAttendances = $query->orderBy('date', 'desc')
                ->orderBy('class_name')
                ->get();

            // Grouping berdasarkan Tanggal, Kelas, Mapel & Guru
            $attendances = $rawAttendances->groupBy(function ($item) {
                $dateVal = Carbon::parse($item->date)->format('Y-m-d');
                $class   = $item->class_name ?? 'Tanpa Kelas';
                $subject = $item->schedule?->subject_id ?? $item->schedule?->subject?->name ?? 'mapel';
                $teacher = $item->schedule?->teacher_code ?? 'guru';

                return "{$dateVal}|{$class}|{$subject}|{$teacher}";
            })->map(function ($items) {
                $sortedItems = $items->sortBy(function ($i) {
                    return (int) $i->time_slot;
                })->values();

                $timeSlots = $sortedItems->pluck('time_slot')->map(fn($v) => (int) $v)->unique()->sort()->values();

                $jamStart = $timeSlots->first() ?? 1;
                $jamEnd   = $timeSlots->last() ?? 1;

                $jamText = ($jamStart == $jamEnd) 
                    ? "Jam ke-{$jamStart}" 
                    : "Jam ke-{$jamStart} s.d {$jamEnd}";

                $firstWithSchedule = $sortedItems->first(fn($i) => !is_null($i->schedule)) ?? $sortedItems->first();

                $subjectName = $firstWithSchedule->schedule?->subject?->name 
                            ?? $firstWithSchedule->schedule?->subject?->nama_mapel 
                            ?? 'Tanpa Mapel';

                $teacherName = $firstWithSchedule->schedule?->teacher?->name 
                            ?? 'Tanpa Guru Utama';

                $substituteName = $sortedItems->first()->substituteTeacher?->name ?? '-';

                return (object) [
                    'date'               => Carbon::parse($sortedItems->first()->date)->format('Y-m-d'),
                    'class_name'         => $sortedItems->first()->class_name,
                    'subject_name'       => $subjectName,
                    'teacher_name'       => $teacherName,
                    'jam_text'           => $jamText,
                    'time_slot'          => $jamText,
                    'total_jp'           => $timeSlots->count(),
                    'status'             => $sortedItems->first()->status,
                    'substitute_teacher' => $substituteName,
                    'substituteTeacher'  => (object) ['name' => $substituteName],
                    'notes'              => $sortedItems->first()->task_description ?? '-',
                    'items'              => $sortedItems,
                ];
            });

            $stats = [
                'hadir'      => $rawAttendances->where('status', 'hadir')->count(),
                'terlambat'  => $rawAttendances->where('status', 'terlambat')->count(),
                'izin_sakit' => $rawAttendances->whereIn('status', ['izin', 'sakit'])->count(),
                'alpa'       => $rawAttendances->where('status', 'alpa')->count(),
            ];

        } else {
            $query = Attendance::with('recorder')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate);

            if ($type && $type !== 'kbm') {
                $query->where('type', $type);
            }

            if ($status) {
                $query->where('status', strtolower($status));
            }

            if ($selectedClass && $selectedClass !== '-- Semua Kelas --') {
                $query->where('class_or_subject', $selectedClass);
            }

            $attendances = $query->latest('date')->get();

            $stats = [
                'terlambat' => $attendances->where('status', 'terlambat')->count(),
                'izin'      => $attendances->where('status', 'izin')->count(),
                'sakit'     => $attendances->where('status', 'sakit')->count(),
                'alpa'      => $attendances->where('status', 'alpa')->count(),
            ];
        }

        // Ambil daftar semua kelas umum untuk filter rekap
        $classesFromStudents = Student::pluck('class')->filter()->toArray();
        $classesFromKbm      = ClassroomAttendance::pluck('class_name')->filter()->toArray();
        $classesFromJournal  = TeacherJournal::pluck('class_name')->filter()->toArray();

        $classesFromModel = [];
        if (class_exists('\App\Models\SchoolClass')) {
            $classesFromModel = \App\Models\SchoolClass::pluck('name')->toArray();
        } elseif (class_exists('\App\Models\Kelas')) {
            $classesFromModel = \App\Models\Kelas::pluck('nama_kelas')->toArray();
        }

        $classes = array_values(array_unique(array_merge(
            $classesFromModel, 
            $classesFromStudents, 
            $classesFromKbm, 
            $classesFromJournal
        )));
        sort($classes);

        // Ambil kelas khusus guru yang login
        $teacherClasses = $this->getTeacherClasses();

        // Ambil daftar mata pelajaran
        $teacherSubjects = [];
        if (class_exists('\App\Models\Subject')) {
            $teacherSubjects = Subject::pluck('name')->filter()->toArray();
            if (empty($teacherSubjects)) {
                $teacherSubjects = Subject::pluck('nama_mapel')->filter()->toArray();
            }
            $teacherSubjects = array_values(array_unique($teacherSubjects));
        }
        sort($teacherSubjects);

        return view('reports.index', compact(
            'attendances', 
            'journals', 
            'stats', 
            'startDate', 
            'endDate', 
            'type', 
            'status', 
            'classes', 
            'selectedClass',
            'teacherClasses',
            'teacherSubjects'
        ));
    }

    /**
     * Router Unduh Dokumen Resmi Per Kelas (Mendukung Cetak PDF & Excel)
     */
    public function downloadDoc(Request $request, $type)
    {
        $format = $request->input('format', 'print');

        if ($format === 'excel') {
            return $this->exportDocExcel($request, $type);
        }

        switch ($type) {
            case 'daftar-hadir':
                return $this->downloadDaftarHadirBulanan($request);

            case 'rekap-presensi':
                return $this->downloadRekapPresensi($request);

            case 'blanko-nilai':
                return $this->downloadBlankoNilai($request);

            default:
                abort(404, 'Tipe dokumen tidak ditemukan.');
        }
    }

    public function downloadDaftarHadirBulanan(Request $request)
    {
        $teacherClasses = $this->getTeacherClasses();
        $defaultClass   = reset($teacherClasses) ?: 'Semua Kelas';
        $class          = $request->input('class', $defaultClass);

        $students = Student::when($class && $class !== '-- Semua Kelas --', fn($q) => $q->where('class', $class))
            ->orderBy('name', 'asc')
            ->get();

        return view('reports.docs.daftar-hadir', compact('class', 'students', 'teacherClasses'));
    }

    public function downloadRekapPresensi(Request $request)
    {
        $teacherClasses = $this->getTeacherClasses();
        $defaultClass   = reset($teacherClasses) ?: 'Semua Kelas';
        $class          = $request->input('class', $defaultClass);

        $students = Student::when($class && $class !== '-- Semua Kelas --', fn($q) => $q->where('class', $class))
            ->orderBy('name', 'asc')
            ->get();

        return view('reports.docs.rekap-presensi', compact('class', 'students', 'teacherClasses'));
    }

    public function downloadBlankoNilai(Request $request)
    {
        $teacherClasses = $this->getTeacherClasses();
        $defaultClass   = reset($teacherClasses) ?: 'Semua Kelas';
        $class          = $request->input('class', $defaultClass);
        $subject        = $request->input('subject', 'Mata Pelajaran');

        $students = Student::when($class && $class !== '-- Semua Kelas --', fn($q) => $q->where('class', $class))
            ->orderBy('name', 'asc')
            ->get();

        return view('reports.docs.blanko-nilai', compact('class', 'subject', 'students', 'teacherClasses'));
    }

    /**
     * Export Dokumen Resmi Per Kelas ke Format Excel (.xls)
     */
    private function exportDocExcel(Request $request, $type)
    {
        $class   = $request->input('class', 'Semua_Kelas');
        $subject = $request->input('subject', 'Mata_Pelajaran');
        $fileName = str_replace('-', '_', $type) . '_' . str_replace(' ', '_', $class) . '.xls';

        $headers = [
            "Content-Type"        => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$fileName}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $students = Student::when($class && $class !== '-- Semua Kelas --', fn($q) => $q->where('class', $class))
            ->orderBy('name', 'asc')
            ->get();

        $callback = function () use ($type, $class, $subject, $students) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style>
                    th { background-color: #f3f4f6; color: #111827; font-weight: bold; border: 0.5pt solid #000; padding: 5px; text-align: center; }
                    td { border: 0.5pt solid #000; padding: 5px; vertical-align: middle; }
                    .title { font-size: 14pt; font-weight: bold; text-align: center; }
                    .subtitle { font-size: 11pt; font-weight: bold; text-align: center; }
                </style>
            </head>
            <body>';

            if ($type === 'daftar-hadir') {
                $html .= '<table border="1">
                    <tr><td colspan="38" class="title">DAFTAR HADIR SISWA BULANAN - KELAS ' . htmlspecialchars($class) . '</td></tr>
                    <tr><td colspan="38"></td></tr>
                    <thead>
                        <tr>
                            <th width="40">NO</th>
                            <th width="100">NIS / NISN</th>
                            <th width="250">NAMA SISWA</th>
                            <th width="30">L/P</th>';
                for ($d = 1; $d <= 31; $d++) {
                    $html .= '<th width="25">' . $d . '</th>';
                }
                $html .= '<th width="30">S</th><th width="30">I</th><th width="30">A</th></tr>
                    </thead>
                    <tbody>';
                foreach ($students as $i => $s) {
                    $html .= '<tr>
                        <td style="text-align:center;">' . ($i + 1) . '</td>
                        <td style="text-align:center;">' . ($s->nis ?? '-') . '</td>
                        <td>' . htmlspecialchars($s->name) . '</td>
                        <td style="text-align:center;">' . ($s->gender ?? 'L') . '</td>';
                    for ($d = 1; $d <= 31; $d++) { $html .= '<td></td>'; }
                    $html .= '<td></td><td></td><td></td></tr>';
                }
                $html .= '</tbody></table>';

            } elseif ($type === 'rekap-presensi') {
                $html .= '<table border="1">
                    <tr><td colspan="8" class="title">REKAP PRESENSI SISWA - KELAS ' . htmlspecialchars($class) . '</td></tr>
                    <tr><td colspan="8"></td></tr>
                    <thead>
                        <tr>
                            <th width="40">NO</th>
                            <th width="120">NIS / NISN</th>
                            <th width="250">NAMA SISWA</th>
                            <th width="60">HADIR</th>
                            <th width="60">SAKIT</th>
                            <th width="60">IZIN</th>
                            <th width="60">ALPA</th>
                            <th width="100">% KEHADIRAN</th>
                        </tr>
                    </thead>
                    <tbody>';
                foreach ($students as $i => $s) {
                    $html .= '<tr>
                        <td style="text-align:center;">' . ($i + 1) . '</td>
                        <td style="text-align:center;">' . ($s->nis ?? '-') . '</td>
                        <td>' . htmlspecialchars($s->name) . '</td>
                        <td style="text-align:center;">0</td>
                        <td style="text-align:center;">0</td>
                        <td style="text-align:center;">0</td>
                        <td style="text-align:center;">0</td>
                        <td style="text-align:center;">100%</td>
                    </tr>';
                }
                $html .= '</tbody></table>';

            } else { // blanko-nilai
                $html .= '<table border="1">
                    <tr><td colspan="10" class="title">BLANKO NILAI MATA PELAJARAN: ' . strtoupper(htmlspecialchars($subject)) . '</td></tr>
                    <tr><td colspan="10" class="subtitle">KELAS: ' . htmlspecialchars($class) . '</td></tr>
                    <tr><td colspan="10"></td></tr>
                    <thead>
                        <tr>
                            <th width="40" rowspan="2">NO</th>
                            <th width="250" rowspan="2">NAMA SISWA</th>
                            <th colspan="4">NILAI TUGAS / FORMATIF</th>
                            <th colspan="2">SUMATIF</th>
                            <th width="80" rowspan="2">NILAI AKHIR</th>
                            <th width="120" rowspan="2">CAPAIAN / CATATAN</th>
                        </tr>
                        <tr>
                            <th width="50">T1</th><th width="50">T2</th><th width="50">T3</th><th width="50">T4</th>
                            <th width="60">STS</th><th width="60">SAS</th>
                        </tr>
                    </thead>
                    <tbody>';
                if ($students->count() > 0) {
                    foreach ($students as $i => $s) {
                        $html .= '<tr>
                            <td style="text-align:center;">' . ($i + 1) . '</td>
                            <td>' . htmlspecialchars($s->name) . '</td>
                            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        </tr>';
                    }
                } else {
                    $html .= '<tr><td colspan="10" style="text-align:center;">Tidak ada data siswa untuk kelas ini.</td></tr>';
                }
                $html .= '</tbody></table>';
            }

            $html .= '</body></html>';
            fwrite($output, $html);
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Halaman Print Cetak Laporan Utama
     */
    public function print(Request $request)
    {
        $startDateInput = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDateInput   = $request->input('end_date', now()->toDateString());

        $startDate     = Carbon::parse($startDateInput)->format('Y-m-d');
        $endDate       = Carbon::parse($endDateInput)->format('Y-m-d');

        $type          = $request->input('type', 'kbm');
        $selectedClass = $request->input('class_name');

        Carbon::setLocale('id');

        if ($type === 'jurnal') {
            $query = TeacherJournal::with([
                'teacher', 
                'subject', 
                'schedule.teacher', 
                'schedule.subject'
            ])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

            if ($selectedClass && $selectedClass !== '-- Semua Kelas --') {
                $query->where(function($q) use ($selectedClass) {
                    $q->where('class_name', $selectedClass)
                      ->orWhereHas('schedule', function($s) use ($selectedClass) {
                          $s->where('class_name', $selectedClass);
                      });
                });
            }

            $rawJournals = $query->orderBy('date', 'asc')->get();

            $data = $rawJournals->map(function ($j) {
                $j->display_class    = $j->class_name ?? $j->schedule?->class_name ?? '-';
                $j->display_teacher  = $j->teacher?->name ?? $j->schedule?->teacher?->name ?? '-';
                $j->display_subject  = $j->subject?->name ?? $j->subject?->nama_mapel ?? $j->schedule?->subject?->name ?? $j->schedule?->subject?->nama_mapel ?? '-';
                $j->display_materi   = $j->material ?? $j->materi_pokok ?? $j->materi_pembelajaran ?? $j->materi ?? $j->description ?? '-';
                $j->display_hambatan = $j->notes ?? $j->hambatan ?? $j->catatan ?? $j->keterangan ?? '-';
                return $j;
            });

        } elseif ($type === 'kbm') {
            $raw = ClassroomAttendance::with([
                'schedule.teacher', 
                'schedule.subject', 
                'substituteTeacher'
            ])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate)
            ->when($selectedClass && $selectedClass !== '-- Semua Kelas --', fn($q) => $q->where('class_name', $selectedClass))
            ->orderBy('date', 'asc')
            ->orderBy('class_name')
            ->get();

            $data = $raw->groupBy(function ($item) {
                $dateVal = Carbon::parse($item->date)->format('Y-m-d');
                $class   = $item->class_name ?? 'Tanpa Kelas';
                $subject = $item->schedule?->subject_id ?? $item->schedule?->subject?->name ?? 'mapel';
                $teacher = $item->schedule?->teacher_code ?? 'guru';

                return "{$dateVal}|{$class}|{$subject}|{$teacher}";
            });

        } else {
            $data = Attendance::whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate)
                ->when($type !== 'kbm', function ($q) use ($type) {
                    return $q->where('type', $type);
                })
                ->when($selectedClass && $selectedClass !== '-- Semua Kelas --', fn($q) => $q->where('class_or_subject', $selectedClass))
                ->orderBy('date', 'asc')
                ->get();
        }

        return view('reports.print', compact('data', 'startDate', 'endDate', 'type'));
    }

    /**
     * Export Laporan Utama ke Format Excel (.xls)
     */
    public function export(Request $request)
    {
        $startDateInput = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDateInput   = $request->input('end_date', now()->toDateString());

        $startDate     = Carbon::parse($startDateInput)->format('Y-m-d');
        $endDate       = Carbon::parse($endDateInput)->format('Y-m-d');

        $type          = $request->input('type', 'kbm');
        $status        = $request->input('status');
        $selectedClass = $request->input('class_name');

        $fileName = 'laporan_' . $type . '_' . $startDate . '_sd_' . $endDate . '.xls';

        $headers = [
            "Content-Type"        => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$fileName}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $journals    = null;
        $attendances = null;

        if ($type === 'jurnal') {
            $query = TeacherJournal::with([
                'teacher', 
                'subject', 
                'schedule.teacher', 
                'schedule.subject'
            ])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

            if ($selectedClass && $selectedClass !== '-- Semua Kelas --') {
                $query->where(function($q) use ($selectedClass) {
                    $q->where('class_name', $selectedClass)
                      ->orWhereHas('schedule', function($s) use ($selectedClass) {
                          $s->where('class_name', $selectedClass);
                      });
                });
            }

            $journals = $query->orderBy('date', 'asc')->get();

        } elseif ($type === 'kbm') {
            $query = ClassroomAttendance::with([
                'schedule.teacher', 
                'schedule.subject', 
                'substituteTeacher', 
                'piketUser'
            ])
            ->whereDate('date', '>=', $startDate)
            ->whereDate('date', '<=', $endDate);

            if ($status) {
                $query->where('status', strtolower($status));
            }
            if ($selectedClass && $selectedClass !== '-- Semua Kelas --') {
                $query->where('class_name', $selectedClass);
            }

            $attendances = $query->orderBy('date', 'asc')->orderBy('class_name')->get();
        } else {
            $query = Attendance::with('recorder')
                ->whereDate('date', '>=', $startDate)
                ->whereDate('date', '<=', $endDate);

            if ($type && $type !== 'kbm') {
                $query->where('type', $type);
            }
            if ($status) {
                $query->where('status', strtolower($status));
            }
            if ($selectedClass && $selectedClass !== '-- Semua Kelas --') {
                $query->where('class_or_subject', $selectedClass);
            }

            $attendances = $query->latest('date')->get();
        }

        $callback = function () use ($type, $startDate, $endDate, $journals, $attendances) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            $colSpan = ($type === 'jurnal') ? '6' : (($type === 'kbm') ? '8' : '7');
            $title   = ($type === 'jurnal') ? 'REKAPITULASI JURNAL MENGAJAR GURU' : (($type === 'kbm') ? 'REKAPITULASI PIKET PRESENSI KBM' : 'REKAPITULASI CATATAN PIKET');

            $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style>
                    th { background-color: #f3f4f6; color: #111827; font-weight: bold; text-align: center; border: 0.5pt solid #000000; padding: 5px; }
                    td { border: 0.5pt solid #000000; vertical-align: middle; padding: 5px; }
                    .title { font-size: 16pt; font-weight: bold; text-align: center; }
                    .subtitle { font-size: 11pt; text-align: center; }
                </style>
            </head>
            <body>
                <table border="1">
                    <tr><td colspan="' . $colSpan . '" class="title">LAPORAN ' . $title . '</td></tr>
                    <tr><td colspan="' . $colSpan . '" class="subtitle">Periode: ' . date('d/m/Y', strtotime($startDate)) . ' s.d. ' . date('d/m/Y', strtotime($endDate)) . '</td></tr>
                    <tr><td colspan="' . $colSpan . '"></td></tr>';

            if ($type === 'jurnal') {
                $html .= '
                <thead>
                    <tr>
                        <th width="100">Tanggal</th>
                        <th width="80">Kelas</th>
                        <th width="200">Guru Mengajar</th>
                        <th width="200">Mata Pelajaran</th>
                        <th width="300">Materi Pokok</th>
                        <th width="250">Catatan / Hambatan</th>
                    </tr>
                </thead>
                <tbody>';
                
                if ($journals && $journals->count() > 0) {
                    foreach ($journals as $j) {
                        $displayClass   = $j->class_name ?? $j->schedule?->class_name ?? '-';
                        $displayTeacher = $j->teacher?->name ?? $j->schedule?->teacher?->name ?? '-';
                        $displaySubject = $j->subject?->name ?? $j->subject?->nama_mapel ?? $j->schedule?->subject?->name ?? $j->schedule?->subject?->nama_mapel ?? '-';
                        $displayMateri  = $j->material ?? $j->materi_pokok ?? $j->materi_pembelajaran ?? $j->materi ?? $j->description ?? '-';
                        $displayNotes   = $j->notes ?? $j->hambatan ?? $j->catatan ?? $j->keterangan ?? '-';

                        $html .= '<tr>
                            <td style="text-align:center;">' . date('d/m/Y', strtotime($j->date)) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($displayClass) . '</td>
                            <td>' . htmlspecialchars($displayTeacher) . '</td>
                            <td>' . htmlspecialchars($displaySubject) . '</td>
                            <td>' . htmlspecialchars($displayMateri) . '</td>
                            <td>' . htmlspecialchars($displayNotes) . '</td>
                        </tr>';
                    }
                } else {
                    $html .= '<tr><td colspan="6" style="text-align:center;">Tidak ada data jurnal.</td></tr>';
                }

            } elseif ($type === 'kbm') {
                $html .= '
                <thead>
                    <tr>
                        <th width="100">Tanggal</th>
                        <th width="80">Kelas</th>
                        <th width="200">Mata Pelajaran</th>
                        <th width="200">Guru Utama</th>
                        <th width="80">Jam Ke</th>
                        <th width="80">Status</th>
                        <th width="200">Guru Pengganti</th>
                        <th width="250">Catatan / Tugas</th>
                    </tr>
                </thead>
                <tbody>';

                if ($attendances && $attendances->count() > 0) {
                    foreach ($attendances as $a) {
                        $html .= '<tr>
                            <td style="text-align:center;">' . date('d/m/Y', strtotime($a->date)) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($a->class_name) . '</td>
                            <td>' . htmlspecialchars($a->schedule?->subject?->name ?? $a->schedule?->subject?->nama_mapel ?? 'Tanpa Mapel') . '</td>
                            <td>' . htmlspecialchars($a->schedule?->teacher?->name ?? 'Tanpa Guru') . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($a->time_slot ?? $a->jam_ke ?? '-') . '</td>
                            <td style="text-align:center; text-transform:uppercase;">' . htmlspecialchars($a->status) . '</td>
                            <td>' . htmlspecialchars($a->substituteTeacher?->name ?? '-') . '</td>
                            <td>' . htmlspecialchars($a->task_description ?? '-') . '</td>
                        </tr>';
                    }
                } else {
                    $html .= '<tr><td colspan="8" style="text-align:center;">Tidak ada data presensi KBM.</td></tr>';
                }

            } else {
                $html .= '
                <thead>
                    <tr>
                        <th width="100">Tanggal</th>
                        <th width="100">Tipe</th>
                        <th width="120">Kelas / Subjek</th>
                        <th width="200">Nama</th>
                        <th width="80">Status</th>
                        <th width="250">Keterangan</th>
                        <th width="150">Petugas Piket</th>
                    </tr>
                </thead>
                <tbody>';

                if ($attendances && $attendances->count() > 0) {
                    foreach ($attendances as $a) {
                        $html .= '<tr>
                            <td style="text-align:center;">' . date('d/m/Y', strtotime($a->date)) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars(ucfirst($a->type)) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($a->class_or_subject ?? '-') . '</td>
                            <td>' . htmlspecialchars($a->name) . '</td>
                            <td style="text-align:center; text-transform:uppercase;">' . htmlspecialchars($a->status) . '</td>
                            <td>' . htmlspecialchars($a->notes ?? '-') . '</td>
                            <td>' . htmlspecialchars($a->recorder?->name ?? 'Sistem') . '</td>
                        </tr>';
                    }
                } else {
                    $html .= '<tr><td colspan="7" style="text-align:center;">Tidak ada data catatan piket.</td></tr>';
                }
            }

            $html .= '
                </tbody>
            </table>
            </body>
            </html>';

            fwrite($output, $html);
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}