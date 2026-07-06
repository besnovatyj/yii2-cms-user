<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\listeners;

use Besnovatyj\User\entities\events\UserSignUpConfirmed;

class UserSignupConfirmedListener
{
//    private $newsletter;

//    public function __construct(Newsletter $newsletter)
//    {
//        $this->newsletter = $newsletter;
//    }

    public function handle(UserSignUpConfirmed $event): void
    {
//        $this->newsletter->subscribe($event->user->email);
    }
}
