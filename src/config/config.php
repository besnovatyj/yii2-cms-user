<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

return [
    'id' => 'User',
    'params' => [
        'iconClass' => 'bi bi-people',

        'cacheDuration' => 3600,
        'defaultUserStatus' => 0,
        'onlyRegisteredRoute' => 0,
        'strict' => 1,
        'globalCacheTag' => 'user.admin',
        'passwordResetTokenExpire' => 3600,
        'rememberMeDuration' => 3600 * 24 * 30,
    ],
];
