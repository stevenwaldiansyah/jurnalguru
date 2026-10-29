<?php

namespace App\Providers;

use App\Models\Journal;
use App\Policies\JournalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Journal::class, JournalPolicy::class);

        Gate::define('manage-master', fn ($user) => $user->canManageMasterData());
        Gate::define('backup-restore', fn ($user) => $user->isAdmin());
        Gate::define('view-recap', fn ($user) => $user->isGuru() || $user->canViewAllJournals());
    }
}
