<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class JournalSearchController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $query = Journal::query()
            ->with(['schedule.schoolClass', 'schedule.subject', 'schedule.teacher', 'schedule.semester'])
            ->join('schedules', 'journals.schedule_id', '=', 'schedules.id')
            ->select('journals.*')
            ->orderByDesc('journals.date');

        if ($user->isGuru()) {
            $query->where('schedules.teacher_id', $user->id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('journals.date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('journals.date', '<=', $request->date_to);
        }
        if ($request->filled('class_id')) {
            $query->where('schedules.class_id', $request->class_id);
        }
        if ($request->filled('semester_id')) {
            $query->where('schedules.semester_id', $request->semester_id);
        }
        if ($user->canViewAllJournals() && $request->filled('teacher_id')) {
            $query->where('schedules.teacher_id', $request->teacher_id);
        }

        $journals = $query->paginate(15)->withQueryString();

        return view('journals.search', [
            'journals' => $journals,
            'classes' => SchoolClass::orderBy('name')->get(),
            'semesters' => Semester::orderByDesc('academic_year')->get(),
            'teachers' => $user->canViewAllJournals()
                ? User::query()->where('role', 'guru')->orderBy('name')->get()
                : collect(),
            'filters' => $request->only(['date_from', 'date_to', 'class_id', 'semester_id', 'teacher_id']),
        ]);
    }
}
