<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\components;

use yii\rbac\Rule;
use yii\web\User;

class GuestRule extends Rule
{
    public $name = 'GuestRule';

    public function execute($user, $item, $params): bool
    {
        /** @var $user User */
        return $user->getIsGuest();
    }
}
