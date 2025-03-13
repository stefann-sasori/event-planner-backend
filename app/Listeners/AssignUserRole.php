<?php

namespace App\Listeners;

use App\Enum\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class AssignUserRole
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        /** @var User $user */
        $user = $event->user;
        $user->assignRole(RoleEnum::USER->value);
    }
}
