<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

return [
    'id' => 'User',
    'params' => [
        'iconClass' => 'bi bi-people',

        'cacheDuration' => 3600,
        'userTable' => '{{%user_users}}',
        'defaultUserStatus' => 10,
        'userRolePageSize' => 100,
        'onlyRegisteredRoute' => false,
        'strict' => true,
        'globalCacheTag' => 'user.admin',
        'passwordResetTokenExpire' => 3600,
        'rememberMeDuration' => 3600 * 24 * 30,

        'directories' => false, // Если для работы модуля необходимы директории для статики
    ],
];
