<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Journal;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JournalStoreService
{
    /**
     * @param  array<string, mixed>  $validated
     */
    public function store(User $teacher, Schedule $schedule, array $validated): Journal
    {
        $this->assertTeacherOwnsSchedule($teacher, $schedule);

        $date = Carbon::parse($validated['date'])->startOfDay();

        $existing = Journal::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $date)
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'date' => 'Jurnal untuk jadwal dan tanggal ini sudah ada.',
            ]);
        }

        $students = Student::query()
            ->where('class_id', $schedule->class_id)
            ->orderBy('name')
            ->get();

        return DB::transaction(function () use ($schedule, $validated, $date, $students) {
            $journal = Journal::create([
                'schedule_id' => $schedule->id,
                'date' => $date,
                'topic' => $validated['topic'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $attendanceInput = $validated['attendance'] ?? [];
            foreach ($students as $student) {
                $status = $attendanceInput[$student->id] ?? AttendanceStatus::Hadir->value;
                $journal->attendances()->create([
                    'student_id' => $student->id,
                    'status' => $status,
                ]);
            }

            foreach ($validated['scores'] ?? [] as $row) {
                if (empty($row['student_id']) || $row['score'] === null || $row['score'] === '') {
                    continue;
                }
                $studentId = (int) $row['student_id'];
                if (! $students->contains('id', $studentId)) {
                    throw ValidationException::withMessages([
                        'scores' => 'Siswa tidak terdaftar di kelas jurnal ini.',
                    ]);
                }
                $journal->scores()->create([
                    'student_id' => $studentId,
                    'type' => $row['type'] ?? 'Tugas',
                    'score' => $row['score'],
                ]);
            }

            return $journal->load(['schedule.schoolClass', 'schedule.subject', 'attendances', 'scores']);
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function update(Journal $journal, User $teacher, array $validated): Journal
    {
        $this->assertTeacherOwnsSchedule($teacher, $journal->schedule);

        $students = Student::query()
            ->where('class_id', $journal->schedule->class_id)
            ->pluck('id');

        return DB::transaction(function () use ($journal, $validated, $students) {
            $journal->update([
                'topic' => $validated['topic'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $attendanceInput = $validated['attendance'] ?? [];
            foreach ($journal->attendances as $attendance) {
                if (! $students->contains($attendance->student_id)) {
                    continue;
                }
                $status = $attendanceInput[$attendance->student_id] ?? $attendance->status->value;
                $attendance->update(['status' => $status]);
            }

            if (array_key_exists('scores', $validated)) {
                $journal->scores()->delete();
                foreach ($validated['scores'] as $row) {
                    if (empty($row['student_id']) || $row['score'] === null || $row['score'] === '') {
                        continue;
                    }
                    $studentId = (int) $row['student_id'];
                    if (! $students->contains($studentId)) {
                        throw ValidationException::withMessages([
                            'scores' => 'Siswa tidak terdaftar di kelas jurnal ini.',
                        ]);
                    }
                    $journal->scores()->create([
                        'student_id' => $studentId,
                        'type' => $row['type'] ?? 'Tugas',
                        'score' => $row['score'],
                    ]);
                }
            }

            return $journal->fresh(['schedule.schoolClass', 'schedule.subject', 'attendances.student', 'scores.student']);
        });
    }

    public function assertTeacherOwnsSchedule(User $teacher, Schedule $schedule): void
    {
        if ((int) $schedule->teacher_id !== (int) $teacher->id) {
            abort(403, 'Jadwal ini bukan milik Anda.');
        }
    }
}
