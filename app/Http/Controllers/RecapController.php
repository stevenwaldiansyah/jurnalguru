<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class RecapController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $semesters = Semester::orderByDesc('academic_year')->get();
        $semesterId = $request->integer('semester_id') ?: $semesters->first()?->id;
        $teacherId = $user->isGuru() ? $user->id : $request->integer('teacher_id');

        $teachers = $user->canViewAllJournals()
            ? User::query()->where('role', 'guru')->orderBy('name')->get()
            : collect();

        $teacher = $teacherId ? User::find($teacherId) : null;
        if ($user->isGuru()) {
            $teacher = $user;
        }

        $schedules = collect();
        $journals = collect();

        if ($semesterId && $teacher) {
            $schedules = Schedule::query()
                ->with(['schoolClass', 'subject', 'semester'])
                ->where('semester_id', $semesterId)
                ->where('teacher_id', $teacher->id)
                ->orderBy('day')
                ->orderBy('start_time')
                ->get();

            $journals = Journal::query()
                ->with([
                    'schedule.schoolClass',
                    'schedule.subject',
                    'attendances',
                    'scores',
                ])
                ->whereIn('schedule_id', $schedules->pluck('id'))
                ->orderBy('date')
                ->get()
                ->groupBy('schedule_id');
        }

        return view('recap.index', [
            'semesters' => $semesters,
            'semesterId' => $semesterId,
            'teacher' => $teacher,
            'teachers' => $teachers,
            'schedules' => $schedules,
            'journalsBySchedule' => $journals,
        ]);
    }

    public function pdf(Request $request): Response
    {
        $user = Auth::user();
        $semesterId = $request->integer('semester_id');
        $teacherId = $user->isGuru() ? $user->id : $request->integer('teacher_id');

        if (! $semesterId || ! $teacherId) {
            abort(422, 'Semester dan guru wajib dipilih.');
        }

        if ($user->isGuru() && (int) $teacherId !== (int) $user->id) {
            abort(403);
        }

        $teacher = User::findOrFail($teacherId);
        $semester = Semester::findOrFail($semesterId);

        $schedules = Schedule::query()
            ->with(['schoolClass', 'subject'])
            ->where('semester_id', $semesterId)
            ->where('teacher_id', $teacher->id)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        $journals = Journal::query()
            ->with(['attendances', 'scores'])
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->orderBy('date')
            ->get()
            ->groupBy('schedule_id');

        $pdf = Pdf::loadView('recap.pdf', [
            'teacher' => $teacher,
            'semester' => $semester,
            'schedules' => $schedules,
            'journalsBySchedule' => $journals,
        ])->setPaper('a4', 'portrait');

        $fileName = $this->recapPdfFileName($teacher->username, $semester->academic_year);

        return $pdf->download($fileName);
    }

    private function recapPdfFileName(string $username, string $academicYear): string
    {
        $safeUser = preg_replace('/[\/\\\\:*?"<>|]/', '-', $username) ?: 'guru';
        $safeYear = preg_replace('/[\/\\\\:*?"<>|]/', '-', $academicYear) ?: 'semester';

        return "rekap_{$safeUser}_{$safeYear}.pdf";
    }
}
