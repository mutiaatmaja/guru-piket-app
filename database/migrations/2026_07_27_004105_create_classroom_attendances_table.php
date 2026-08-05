<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classroom_attendances', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('class_name');
            $table->integer('time_slot');
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
            $table->string('status');
            $table->foreignId('substitute_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('task_description')->nullable();
            $table->foreignId('piket_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_attendances');
    }
};