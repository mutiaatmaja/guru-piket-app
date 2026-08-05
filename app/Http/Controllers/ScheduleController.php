<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SchedulesImport;

class ScheduleController extends Controller
{
    /**
     * Menampilkan daftar jadwal pelajaran dengan filter.
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $todayName = Carbon::now()->translatedFormat('l');
        $todayDate = Carbon::now()->toDateString();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $timeSlots = range(1, 12);

        $classList = Student::select('class')
            ->whereNotNull('class')
            ->where('class', '!=', '')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');

        if ($classList->isEmpty()) {
            $classList = collect(['X IPA 1', 'X IPA 2', 'XI IPA 1', 'XI IPA 2', 'XII IPA 1']);
        }

        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();

        $query = Schedule::with(['subject', 'teacher']);

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }

        if ($request->filled('time_slot')) {
            $query->where('time_slot', $request->time_slot);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $schedules = $query->orderBy('day')->orderBy('time_slot')->get();

        return view('schedules.index', compact(
            'schedules',
            'subjects',
            'teachers',
            'classList',
            'days',
            'timeSlots',
            'todayName',
            'todayDate'
        ));
    }

    /**
     * Menyimpan data jadwal pelajaran baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'day'        => 'required|string',
            'time_slot'  => 'required|integer',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        // Cek hanya bentrok kelas di jam/hari sama
        $exists = Schedule::where('day', $request->day)
            ->where('class_name', $request->class_name)
            ->where('time_slot', $request->time_slot)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Kelas {$request->class_name} sudah memiliki jadwal pada hari {$request->day} jam ke-{$request->time_slot}.");
        }

        Schedule::create($request->all());

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil ditambahkan!');
    }

    /**
     * Memperbarui data jadwal pelajaran.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'class_name' => 'required|string',
            'day'        => 'required|string',
            'time_slot'  => 'required|integer',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $schedule = Schedule::findOrFail($id);

        $exists = Schedule::where('day', $request->day)
            ->where('class_name', $request->class_name)
            ->where('time_slot', $request->time_slot)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Kelas {$request->class_name} sudah memiliki jadwal lain pada hari {$request->day} jam ke-{$request->time_slot}.");
        }

        $schedule->update($request->all());

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil diperbarui!');
    }

    /**
     * Menghapus jadwal pelajaran dari database.
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil dihapus!');
    }

    /**
     * Mengimpor data jadwal dari file Excel / CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new SchedulesImport, $request->file('file'));
            return redirect()->back()->with('success', 'Jadwal pelajaran berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Export data jadwal pelajaran ke file Excel (.xls).
     */
    public function export(Request $request)
    {
        $fileName = 'jadwal_pelajaran_' . date('Y-m-d') . '.xls';

        $headers = [
            "Content-Type"        => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$fileName}\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0",
        ];

        $query = Schedule::with(['subject', 'teacher']);

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }
        if ($request->filled('class_name')) {
            $query->where('class_name', $request->class_name);
        }

        $schedules = $query->orderBy('day')->orderBy('time_slot')->get();

        $callback = function () use ($schedules) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");

            $html = '
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style>
                    th { background-color: #f3f4f6; color: #111827; font-weight: bold; text-align: center; border: 0.5pt solid #000000; padding: 5px; }
                    td { border: 0.5pt solid #000000; vertical-align: middle; padding: 5px; }
                    .title { font-size: 16pt; font-weight: bold; text-align: center; }
                </style>
            </head>
            <body>
                <table border="1">
                    <tr><td colspan="5" class="title">JADWAL PELAJARAN SEKOLAH</td></tr>
                    <tr><td colspan="5"></td></tr>
                    <thead>
                        <tr>
                            <th width="100">Hari</th>
                            <th width="100">Jam Ke</th>
                            <th width="120">Kelas</th>
                            <th width="200">Mata Pelajaran</th>
                            <th width="200">Guru Pengampu</th>
                        </tr>
                    </thead>
                    <tbody>';

            if ($schedules->count() > 0) {
                foreach ($schedules as $s) {
                    $html .= '
                    <tr>
                        <td style="text-align:center;">' . htmlspecialchars($s->day) . '</td>
                        <td style="text-align:center;">' . htmlspecialchars($s->time_slot) . '</td>
                        <td style="text-align:center;">' . htmlspecialchars($s->class_name) . '</td>
                        <td>' . htmlspecialchars($s->subject?->name ?? '-') . '</td>
                        <td>' . htmlspecialchars($s->teacher?->name ?? '-') . '</td>
                    </tr>';
                }
            } else {
                $html .= '<tr><td colspan="5" style="text-align:center;">Tidak ada data jadwal pelajaran.</td></tr>';
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