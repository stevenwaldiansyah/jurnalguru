<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function index(): View
    {
        $schedules = Schedule::with(['teacher', 'schoolClass', 'subject', 'semester'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->paginate(15);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create(): View
    {
        return view('admin.schedules.form', [
            'schedule' => new Schedule,
            ...$this->formOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Schedule::create($this->validated($request));

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule): View
    {
        return view('admin.schedules.form', [
            'schedule' => $schedule,
            ...$this->formOptions(),
        ]);
    }

    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $schedule->update($this->validated($request));

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        if ($schedule->journals()->exists()) {
            return back()->with('error', 'Jadwal masih memiliki jurnal. Hapus jurnal terlebih dahulu.');
        }
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function formOptions(): array
    {
        return [
            'teachers' => User::query()->where('role', UserRole::Guru)->orderBy('name')->get(),
            'classes' => SchoolClass::orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'semesters' => Semester::orderByDesc('academic_year')->get(),
            'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        ];
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'teacher_id' => ['required', 'exists:users,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'day' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);
    }
}
