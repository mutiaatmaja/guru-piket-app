<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    protected $table = 'attendance_logs';

    protected $fillable = [
        'student_id',
        'user_id',
        'type',
        'reason',
        'time_in',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi ke Model Student
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Relasi ke Model User (Guru Piket)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}