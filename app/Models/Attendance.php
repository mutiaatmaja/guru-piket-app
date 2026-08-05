<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'name',
        'type',               // 'siswa', 'guru', dll.
        'user_id',
        'class_or_subject',
        'lesson_hour_start',
        'lesson_hour_end',
        'status',             // 'hadir', 'terlambat', 'izin', 'sakit', 'alpa'
        'date',
        'notes',
        'recorded_by',
    ];

    /**
     * Formatting/Casting Tipe Data
     */
    protected $casts = [
        'date'              => 'date',
        'lesson_hour_start' => 'integer',
        'lesson_hour_end'   => 'integer',
    ];

    /**
     * Relasi ke User yang mencatat (petugas piket)
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Alias relasi recorder untuk mencegah RelationNotFoundException
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Relasi opsional ke Data Siswa (jika type = 'siswa' dan user_id mengarah ke tabel students)
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'user_id');
    }

    /**
     * Relasi opsional ke Data Guru (jika type = 'guru' dan user_id mengarah ke tabel teachers)
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'user_id');
    }
}