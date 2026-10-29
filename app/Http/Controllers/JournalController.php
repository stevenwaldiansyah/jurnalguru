<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Models\Journal;
use App\Models\Schedule;
use App\Models\Student;
use App\Services\JournalStoreService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function __construct(private JournalStoreService $storeService) {}

    public function create(Request $request, Schedule $schedule): View|RedirectResponse
    {
        $teacher = Auth::user();
        $this->storeService->assertTeacherOwnsSchedule($teacher, $schedule);

        $date = $request->query('date', Carbon::today()->toDateString());
        $parsedDate = Carbon::parse($date)->startOfDay();

        $existing = Journal::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $parsedDate)
            ->first();

        if ($existing) {
            return redirect()->route('journals.show', $existing)
                ->with('info', 'Jurnal untuk tanggal ini sudah ada. Anda dapat mengubahnya di sini.');
        }

        $students = Student::query()
            ->where('class_id', $schedule->class_id)
            ->orderBy('name')
            ->get();

        $schedule->load(['schoolClass', 'subject']);

        return view('journals.form', [
            'schedule' => $schedule,
            'students' => $students,
            'date' => $parsedDate,
            'journal' => null,
            'attendanceStatuses' => AttendanceStatus::cases(),
        ]);
    }

    public function store(Request $request, Schedule $schedule): RedirectResponse
    {
        $teacher = Auth::user();
        Gate::authorize('create', Journal::class);

        $validated = $this->validateJournal($request);

        $journal = $this->storeService->store($teacher, $schedule, $validated);

        return redirect()->route('journals.show', $journal)
            ->with('success', 'Catatan berhasil disimpan.');
    }

    public function show(Journal $journal): View
    {
        Gate::authorize('view', $journal);
        $journal->load([
            'schedule.schoolClass',
            'schedule.subject',
            'schedule.teacher',
            'attendances.student',
            'scores.student',
        ]);

        return view('journals.show', compact('journal'));
    }

    public function edit(Journal $journal): View
    {
        Gate::authorize('update', $journal);
        $journal->load(['schedule.schoolClass', 'schedule.subject', 'attendances', 'scores']);
        $students = Student::query()
            ->where('class_id', $journal->schedule->class_id)
            ->orderBy('name')
            ->get();

        return view('journals.form', [
            'schedule' => $journal->schedule,
            'students' => $students,
            'date' => $journal->date,
            'journal' => $journal,
            'attendanceStatuses' => AttendanceStatus::cases(),
        ]);
    }

    public function update(Request $request, Journal $journal): RedirectResponse
    {
        Gate::authorize('update', $journal);
        $validated = $this->validateJournal($request, updating: true);
        $this->storeService->update($journal, Auth::user(), $validated);

        return redirect()->route('journals.show', $journal)
            ->with('success', 'Catatan berhasil diperbarui.');
    }

    public function destroy(Journal $journal): RedirectResponse
    {
        Gate::authorize('delete', $journal);
        $journal->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Catatan berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validateJournal(Request $request, bool $updating = false): array
    {
        $rules = [
            'topic' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'attendance' => ['required', 'array'],
            'attendance.*' => ['required', 'in:hadir,sakit,izin,alpa'],
            'scores' => ['nullable', 'array'],
            'scores.*.student_id' => ['nullable', 'integer', 'exists:students,id'],
            'scores.*.type' => ['nullable', 'string', 'max:30'],
            'scores.*.score' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];

        if (! $updating) {
            $rules['date'] = ['required', 'date'];
        }

        return $request->validate($rules, [
            'topic.required' => 'Materi/topik pembelajaran wajib diisi.',
            'attendance.required' => 'Data absensi wajib diisi.',
        ]);
    }
}
