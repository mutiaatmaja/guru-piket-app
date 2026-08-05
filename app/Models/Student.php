<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nisn',
        'nis',
        'class',
        'gender',
        'phone', // <-- Ditambahkan ke fillable
        'birth_place',
        'birth_date',
        'religion',
        'parent_name',
        'parent_phone',
        'address',
        'photo',
    ];
}