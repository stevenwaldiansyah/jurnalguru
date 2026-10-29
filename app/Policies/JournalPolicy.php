<?php

namespace App\Policies;

use App\Models\Journal;
use App\Models\User;

class JournalPolicy
{
    public function view(User $user, Journal $journal): bool
    {
        $journal->loadMissing('schedule');

        if ($user->canViewAllJournals()) {
            return true;
        }

        return $user->isGuru()
            && (int) $journal->schedule->teacher_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isGuru();
    }

    public function update(User $user, Journal $journal): bool
    {
        $journal->loadMissing('schedule');

        return $user->isGuru()
            && (int) $journal->schedule->teacher_id === (int) $user->id;
    }

    public function delete(User $user, Journal $journal): bool
    {
        return $this->update($user, $journal);
    }
}
