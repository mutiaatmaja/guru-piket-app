<?php

namespace App\Http\Controllers;

use App\Models\PiketSchedule;
use App\Models\Teacher;
use App\Http\Requests\ImportPiketScheduleRequest;
use App\Imports\PiketSchedulesImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PiketScheduleController extends Controller
{
    /**
     * Tampilkan daftar jadwal piket mingguan.
     */
    public function index()
    {
        // Urutkan jadwal berdasarkan hari kerja
        $daysOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        
        $schedules = PiketSchedule::with('teacher')->get()->sortBy(function($schedule) use ($daysOrder) {
            return array_search($schedule->day_name, $daysOrder);
        });

        $teachers = Teacher::orderBy('name', 'asc')->get();

        return view('piket_schedules.index', compact('schedules', 'teachers'));
    }

    /**
     * Simpan jadwal piket baru secara manual.
     */
    public function store(Request $request)
    {
        $request->validate([
            'teacher_code' => 'required|exists:teachers,teacher_code',
            'day_name'     => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'status'       => 'required|in:aktif,nonaktif',
            'notes'        => 'nullable|string',
        ]);

        PiketSchedule::create([
            'teacher_code' => $request->teacher_code,
            'day_name'     => $request->day_name,
            'status'       => $request->status,
            'notes'        => $request->notes,
        ]);

        return redirect()->route('piket-schedules.index')->with('success', 'Jadwal piket mingguan berhasil ditambahkan!');
    }

    /**
     * Import data jadwal piket dari file Excel/CSV.
     */
    public function import(ImportPiketScheduleRequest $request)
    {
        try {
            Excel::import(new PiketSchedulesImport, $request->file('file'));
            return redirect()->route('piket-schedules.index')->with('success', 'Jadwal piket mingguan berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimport jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Hapus data jadwal piket.
     */
    public function destroy(PiketSchedule $piketSchedule)
    {
        $piketSchedule->delete();
        return redirect()->route('piket-schedules.index')->with('success', 'Jadwal piket berhasil dihapus!');
    }
}