<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'teachers';

    protected $fillable = [
        'user_id',
        'teacher_code', // <-- Diselaraskan dengan migration
        'name',
        'nip',
        'nik',
        'subject',
        'gender',
        'phone',
        'religion',
        'address',
        'last_education',
        'additional_task',
        'photo',
    ];

    /**
     * Relasi ke Model User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Jadwal KBM Guru
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    /**
     * Relasi ke Jurnal Mengajar Guru
     */
    public function journals()
    {
        return $this->hasMany(TeacherJournal::class, 'teacher_id');
    }

    /**
     * Relasi ke Jadwal Piket Guru
     */
    public function piketSchedules()
    {
        return $this->hasMany(PiketSchedule::class, 'teacher_id');
    }
}