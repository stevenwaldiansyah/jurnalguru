<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('major', 50);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique();
            $table->string('name', 100);
            $table->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $table->timestamps();
            $table->index('class_id');
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('academic_year', 20);
            $table->timestamps();
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('class_id')->constrained('classes')->restrictOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->enum('day', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->index(['teacher_id', 'day']);
            $table->index('semester_id');
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->restrictOnDelete();
            $table->date('date');
            $table->string('topic', 255);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['schedule_id', 'date']);
            $table->index('date');
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa'])->default('hadir');
            $table->timestamps();
            $table->unique(['journal_id', 'student_id']);
        });

        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->string('type', 30);
            $table->decimal('score', 5, 2);
            $table->timestamps();
            $table->index(['journal_id', 'student_id']);
        });

        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->dateTime('back_time');
            $table->enum('status', ['berhasil', 'gagal']);
            $table->string('file_name', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
        Schema::dropIfExists('scores');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('journals');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
    }
};
