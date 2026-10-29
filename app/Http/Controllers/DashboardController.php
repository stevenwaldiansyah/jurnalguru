<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Journal;
use App\Services\JournalReminderService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private JournalReminderService $reminders) {}

    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        return match ($user->role) {
            UserRole::Guru => $this->guruDashboard($user),
            UserRole::Kepsek => view('dashboard.kepsek', ['user' => $user]),
            UserRole::Tu => view('dashboard.tu', ['user' => $user]),
            UserRole::Admin => view('dashboard.admin', ['user' => $user]),
        };
    }

    private function guruDashboard($user): View
    {
        $todaySchedules = $this->reminders->todaySchedules($user);
        $journalsToday = Journal::query()
            ->whereIn('schedule_id', $todaySchedules->pluck('id'))
            ->whereDate('date', Carbon::today())
            ->get()
            ->keyBy('schedule_id');

        $filledCount = $journalsToday->count();
        $pendingToday = $todaySchedules->count() - $filledCount;

        return view('dashboard.guru', [
            'user' => $user,
            'today' => Carbon::now()->locale('id')->translatedFormat('l, d F Y'),
            'schedules' => $todaySchedules,
            'journalsToday' => $journalsToday,
            'filledCount' => $filledCount,
            'pendingToday' => max(0, $pendingToday),
            'reminders' => $this->reminders->pendingReminders($user),
        ]);
    }
}
