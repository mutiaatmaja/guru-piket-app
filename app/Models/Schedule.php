<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'class_name',
        'subject_id',
        'teacher_id',
        'day',
        'time_slot',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'time_slot' => 'integer',
    ];

    /**
     * Relasi ke Mata Pelajaran (Subject)
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relasi ke Guru Mengajar (Teacher)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Relasi ke Jurnal Mengajar Guru
     */
    public function journals()
    {
        return $this->hasMany(TeacherJournal::class, 'schedule_id');
    }

    /**
     * Relasi ke Presensi KBM Kelas
     */
    public function attendances()
    {
        return $this->hasMany(ClassroomAttendance::class, 'schedule_id');
    }
}