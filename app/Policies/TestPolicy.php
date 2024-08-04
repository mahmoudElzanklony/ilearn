<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TestPolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
    public function index(User $user): bool
    {

        return $user->can(['facility-facility-settings']);
    }

}
