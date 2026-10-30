<?php

namespace App\Policies;

use App\Models\User;

/**
 * Placeholder for the upcoming student-only channels feature (no Channel model yet).
 *
 * IMPORTANT: channels are student-only spaces. Teachers must ALWAYS be denied access,
 * including read access. Keep this rule when the real policy is implemented.
 */
class ChannelPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isAllowed($user);
    }

    public function view(User $user): bool
    {
        return $this->isAllowed($user);
    }

    public function create(User $user): bool
    {
        return $this->isAllowed($user);
    }

    private function isAllowed(User $user): bool
    {
        if ($user->isTeacher()) {
            return false;
        }

        return $user->is_active;
    }
}
