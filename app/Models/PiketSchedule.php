<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PiketSchedule extends Model
{
    use HasFactory;

    protected $table = 'piket_schedules';

    protected $fillable = [
        'teacher_id',
        'day_name',
        'status',
        'notes',
    ];

    /**
     * Relasi ke Model Teacher
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }
}