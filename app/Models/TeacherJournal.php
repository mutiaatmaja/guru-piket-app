<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherJournal extends Model
{
    use HasFactory;

    protected $table = 'teacher_journals';

    protected $fillable = [
        'teacher_id',
        'schedule_id',
        'subject_id',
        'class_name',
        'date',
        'material',
        'materi_pokok',  
        'hambatan',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi ke Model Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Relasi ke Model Subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relasi ke Model Schedule
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}