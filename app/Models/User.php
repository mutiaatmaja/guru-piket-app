<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Relasi ke Model Teacher (One to One)
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    /**
     * Relasi ke Record Catatan Presensi yang dicatat oleh User ini
     */
    public function recordedAttendances()
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }

    /**
     * Relasi ke Log Presensi yang dibuat oleh Petugas
     */
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'user_id');
    }

    /**
     * Relasi saat User bertindak sebagai Guru Pengganti KBM
     */
    public function substituteAttendances()
    {
        return $this->hasMany(ClassroomAttendance::class, 'substitute_teacher_id');
    }

    /**
     * Relasi saat User bertindak sebagai Petugas Piket KBM
     */
    public function piketAttendances()
    {
        return $this->hasMany(ClassroomAttendance::class, 'piket_user_id');
    }
}