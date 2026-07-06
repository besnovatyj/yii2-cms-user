<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\entities\events;

use Besnovatyj\User\entities\User;

class UserPasswordResetRequested
{
    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
