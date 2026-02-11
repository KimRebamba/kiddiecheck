<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\User;

class TestPolicy
{
    public function finalize(User $user, Test $test): bool
    {
        if ($test->status !== 'in_progress') { return false; }
        if ($user->id !== $test->observer_id) { return false; }
        if ($user->role === 'teacher') {
            // must still be assigned
            // Explicitly filter on pivot table column to avoid alias issues
            $assigned = $test->student->teachers()
                ->where('teacher_id', $user->id)
                ->where('teacher_student.status','active')
                ->exists();
            if (!$assigned) { return false; }
        }
        // must be within active period
        if (!$test->assessmentPeriod || !now()->between($test->assessmentPeriod->starts_at, $test->assessmentPeriod->ends_at)) { return false; }
        // must have all domains complete
        return $test->isDomainsComplete();
    }

    public function archive(User $user, Test $test): bool
    {
        // Only admin can archive, and only finalized/completed/cancelled
        return $user->role === 'admin' && in_array($test->status, ['finalized','completed','cancelled']);
    }

    public function view(User $user, Test $test): bool
    {
        if ($user->role === 'admin') {
            return $test->status !== 'archived';
        }
        return in_array($test->status, ['finalized','completed']);
    }
}
