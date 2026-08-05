<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassroomAttendance extends Model
{
    use HasFactory;

    protected $table = 'classroom_attendances';

    protected $fillable = [
        'date',
        'class_name',
        'time_slot',
        'schedule_id',
        'status',
        'substitute_teacher_id',
        'task_description',
        'piket_user_id',
    ];

    protected $casts = [
        'date'      => 'date',
        'time_slot' => 'integer',
    ];

    /**
     * Relasi ke Jadwal KBM (Schedules)
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    /**
     * Relasi ke Guru Pengganti (User)
     */
    public function substituteTeacher()
    {
        return $this->belongsTo(User::class, 'substitute_teacher_id');
    }

    /**
     * Relasi ke Petugas Piket (User)
     */
    public function piketUser()
    {
        return $this->belongsTo(User::class, 'piket_user_id');
    }
}