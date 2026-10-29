<?php

namespace App\Services;

use App\Models\Journal;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class JournalReminderService
{
    private const DAY_MAP = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu',
    ];

    public function todayIndonesianDay(): string
    {
        $english = Carbon::now()->format('l');

        return self::DAY_MAP[$english] ?? $english;
    }

    /** @return Collection<int, Schedule> */
    public function todaySchedules(User $teacher): Collection
    {
        $day = $this->todayIndonesianDay();

        return Schedule::query()
            ->with(['schoolClass', 'subject', 'semester'])
            ->where('teacher_id', $teacher->id)
            ->where('day', $day)
            ->orderBy('start_time')
            ->get();
    }

    public function journalExistsForToday(Schedule $schedule): bool
    {
        return Journal::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', Carbon::today())
            ->exists();
    }

    public function isScheduleEndedToday(Schedule $schedule): bool
    {
        $end = $this->parseTimeToday((string) $schedule->end_time);

        return Carbon::now()->greaterThanOrEqualTo($end);
    }

    /**
     * Jadwal lewat (maks. 7 hari) tanpa jurnal — untuk panel pengingat.
     *
     * @return Collection<int, array{schedule: Schedule, date: Carbon}>
     */
    public function pendingReminders(User $teacher, int $maxDays = 7): Collection
    {
        $items = collect();
        $schedules = Schedule::query()
            ->with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->get();

        for ($i = 1; $i <= $maxDays; $i++) {
            $date = Carbon::today()->subDays($i);
            $dayName = self::DAY_MAP[$date->format('l')] ?? $date->format('l');

            foreach ($schedules->where('day', $dayName) as $schedule) {
                $end = $this->parseTimeOnDate((string) $schedule->end_time, $date);
                if (Carbon::now()->lessThan($end)) {
                    continue;
                }

                $exists = Journal::query()
                    ->where('schedule_id', $schedule->id)
                    ->whereDate('date', $date)
                    ->exists();

                if (! $exists) {
                    $items->push([
                        'schedule' => $schedule,
                        'date' => $date->copy(),
                    ]);
                }
            }
        }

        return $items->sortBy(fn ($row) => $row['date']->timestamp)->values();
    }

    private function parseTimeToday(string $time): Carbon
    {
        return $this->parseTimeOnDate($time, Carbon::today());
    }

    private function parseTimeOnDate(string $time, Carbon $date): Carbon
    {
        $time = substr($time, 0, 8);

        return $date->copy()->setTimeFromTimeString($time);
    }
}
