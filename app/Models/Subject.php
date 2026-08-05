<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Relasi ke Jadwal KBM (Schedules)
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'subject_id');
    }

    /**
     * Relasi ke Jurnal Mengajar Guru
     */
    public function journals()
    {
        return $this->hasMany(TeacherJournal::class, 'subject_id');
    }
}