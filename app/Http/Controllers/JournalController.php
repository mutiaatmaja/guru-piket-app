<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\TeacherJournal;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JournalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        Carbon::setLocale('id');

        $todayNameIndo = Carbon::now()->isoFormat('dddd'); // Contoh: "Rabu"
        $todayNameEng  = Carbon::now()->format('l');        // Contoh: "Wednesday"
        $todayDate     = Carbon::now()->toDateString();

        // 1. Cari profil Guru dari User yang sedang login secara presisi
        $teacher = Teacher::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->orWhere('name', $user->name)
            ->when($user->teacher_code, function ($q) use ($user) {
                return $q->orWhere('teacher_code', $user->teacher_code);
            })
            ->first();

        // Jika profil guru tidak ditemukan di DB, kembalikan data kosong
        if (!$teacher) {
            return view('journals.index', [
                'todaySchedules'    => collect(),
                'myJournals'        => collect(),
                'todayNameIndo'     => $todayNameIndo,
                'todayDate'         => $todayDate,
                'recentAttendances' => collect(),
                'canAccessAll'      => false,
            ]);
        }

        $teacherId   = $teacher->id;
        $teacherCode = $teacher->teacher_code ?? $user->teacher_code;

        // 2. Query Jadwal Hari Ini
        $schedules = Schedule::with(['subject', 'teacher'])
            ->where(function ($q) use ($teacherId, $teacherCode) {
                $q->where('teacher_id', $teacherId);
                if ($teacherCode) {
                    $q->orWhere('teacher_code', $teacherCode);
                }
            })
            ->where(function ($q) use ($todayNameIndo, $todayNameEng) {
                $q->where('day', $todayNameIndo)
                  ->orWhere('day', strtolower($todayNameIndo))
                  ->orWhere('day', ucfirst($todayNameIndo))
                  ->orWhere('day', $todayNameEng)
                  ->orWhere('day', strtolower($todayNameEng));
            })
            ->orderBy('time_slot')
            ->get();

        // 3. Ambil Jurnal Hari Ini Khusus Guru Ini
        $todayJournals = TeacherJournal::whereDate('date', $todayDate)
            ->where(function ($q) use ($teacherId, $teacherCode) {
                $q->where('teacher_id', $teacherId);
                if ($teacherCode) {
                    $q->orWhere('teacher_code', $teacherCode);
                }
            })
            ->get()
            ->keyBy('schedule_id');

        // 4. Grouping Jadwal Hari Ini
        $groupedSchedules = $schedules->groupBy(function ($item) {
            return $item->class_name . '_' . $item->subject_id . '_' . $item->teacher_id;
        })->map(function ($items) use ($todayJournals) {
            $first = $items->first();

            $jamList = $items->map(function ($item) {
                $val = $item->time_slot ?? $item->jam_ke ?? '';
                preg_match('/\d+/', $val, $matches);
                return isset($matches[0]) ? (int)$matches[0] : $val;
            })->filter()->sort()->values();

            if ($jamList->count() > 1) {
                $jamText = $jamList->first() . ' - ' . $jamList->last();
            } elseif ($jamList->count() === 1) {
                $jamText = $jamList->first();
            } else {
                $jamText = $first->time_slot ?? '-';
            }

            $filledJournal = $todayJournals->get($first->id);

            return [
                'schedule_id'  => $first->id,
                'class_name'   => $first->class_name,
                'subject_id'   => $first->subject_id,
                'subject'      => $first->subject,
                'teacher'      => $first->teacher,
                'teacher_code' => $first->teacher->teacher_code ?? $first->teacher_code,
                'jam_ke'       => $jamText,
                'is_filled'    => !is_null($filledJournal),
                'journal'      => $filledJournal
            ];
        })->values();

        // 5. Riwayat Jurnal Mengajar Khusus Guru Ini
        $myJournals = TeacherJournal::with(['schedule.subject', 'schedule.teacher'])
            ->where(function ($q) use ($teacherId, $teacherCode) {
                $q->where('teacher_id', $teacherId);
                if ($teacherCode) {
                    $q->orWhere('teacher_code', $teacherCode);
                }
            })
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($journal) use ($teacherId) {
                if ($journal->schedule) {
                    $relatedSchedules = Schedule::where('teacher_id', $journal->schedule->teacher_id ?? $teacherId)
                        ->where('class_name', $journal->schedule->class_name)
                        ->where('subject_id', $journal->schedule->subject_id)
                        ->get();

                    $jamList = $relatedSchedules->map(function ($item) {
                        $val = $item->time_slot ?? $item->jam_ke ?? '';
                        preg_match('/\d+/', $val, $matches);
                        return isset($matches[0]) ? (int)$matches[0] : $val;
                    })->filter()->sort()->values();

                    if ($jamList->count() > 1) {
                        $journal->formatted_jam_ke = $jamList->first() . ' - ' . $jamList->last();
                    } elseif ($jamList->count() === 1) {
                        $journal->formatted_jam_ke = $jamList->first();
                    } else {
                        $journal->formatted_jam_ke = $journal->schedule->time_slot ?? '-';
                    }
                } else {
                    $journal->formatted_jam_ke = '-';
                }
                return $journal;
            });

        return view('journals.index', [
            'todaySchedules'    => $groupedSchedules,
            'myJournals'        => $myJournals,
            'todayNameIndo'     => $todayNameIndo,
            'todayDate'         => $todayDate,
            'recentAttendances' => collect(),
            'canAccessAll'      => false,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'        => 'required|date',
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $user = auth()->user();

        $teacher = Teacher::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->orWhere('name', $user->name)
            ->first();

        $teacherCode = $teacher->teacher_code ?? $user->teacher_code ?? 'GURU';
        $teacherId   = $teacher->id ?? null;

        // Ambil data schedule untuk menarik nama kelas
        $schedule = Schedule::find($request->schedule_id);

        TeacherJournal::updateOrCreate(
            [
                'schedule_id'  => $request->schedule_id,
                'date'         => $request->date,
            ],
            [
                'teacher_id'   => $teacherId,
                'teacher_code' => $teacherCode,
                // Kolom penting yang memicu NOT NULL Constraint Violation:
                'class_name'   => $schedule?->class_name ?? '-',
                'material'     => $request->materi_pokok ?? $request->material ?? '-',
                'notes'        => $request->hambatan ?? $request->notes ?? null,
            ]
        );

        return redirect()->back()->with('success', 'Jadwal / Jurnal mengajar berhasil disimpan!');
    }

    public function printPdf(Request $request)
    {
        $user = auth()->user();

        $teacher = Teacher::where('user_id', $user->id)
            ->orWhere('email', $user->email)
            ->orWhere('name', $user->name)
            ->first();

        $teacherCode = $teacher->teacher_code ?? $user->teacher_code;
        $teacherId   = $teacher->id ?? null;

        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate   = $request->query('end_date', date('Y-m-t'));

        $journals = TeacherJournal::with(['schedule.subject', 'schedule.teacher'])
            ->whereBetween('date', [$startDate, $endDate])
            ->where(function ($q) use ($teacherId, $teacherCode) {
                $q->where('teacher_id', $teacherId);
                if ($teacherCode) {
                    $q->orWhere('teacher_code', $teacherCode);
                }
            })
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($journal) use ($teacherId) {
                if ($journal->schedule) {
                    $relatedSchedules = Schedule::where('teacher_id', $journal->schedule->teacher_id ?? $teacherId)
                        ->where('class_name', $journal->schedule->class_name)
                        ->where('subject_id', $journal->schedule->subject_id)
                        ->get();

                    $jamList = $relatedSchedules->map(function ($item) {
                        $val = $item->time_slot ?? $item->jam_ke ?? '';
                        preg_match('/\d+/', $val, $matches);
                        return isset($matches[0]) ? (int)$matches[0] : $val;
                    })->filter()->sort()->values();

                    if ($jamList->count() > 1) {
                        $journal->formatted_jam_ke = $jamList->first() . ' - ' . $jamList->last();
                    } elseif ($jamList->count() === 1) {
                        $journal->formatted_jam_ke = $jamList->first();
                    } else {
                        $journal->formatted_jam_ke = $journal->schedule->time_slot ?? '-';
                    }
                } else {
                    $journal->formatted_jam_ke = '-';
                }
                return $journal;
            });

        return view('journals.pdf', [
            'journals'  => $journals,
            'teacher'   => $teacher ?? $user,
            'startDate' => $startDate,
            'endDate'   => $endDate
        ]);
    }
}
