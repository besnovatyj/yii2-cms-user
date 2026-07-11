<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\User\entities\Identity;
use Besnovatyj\User\security\RbacAuthorizer;

/**
 * Пер-аппликационные вклады модуля пользователей (контракт ProvidesAppConfig).
 *
 * Модуль ОЖИВЛЯЕТ закрытые приложения, перекрывая ядровые дефолты:
 *  - `user.identityClass` GuestIdentity → рабочий {@see Identity} (появляется возможность входа);
 *  - `accessAuthorizer` DenyAllAuthorizer → {@see RbacAuthorizer} (гейт начинает пускать по правам RBAC);
 *  - `allowActions` — маршруты входа/выхода в whitelist гейта.
 *
 * Вклад — частичное дерево конфига приложения (ключи 1:1 с Yii). Класс гейта `as access.class`
 * модуль не задаёт: он принадлежит ядру, а allowlist-политика компилятора всё равно вырезала бы его
 * (см. ProvidesAppConfig). Модуль лишь ДОПОЛНЯЕТ `as access.allowActions` маршрутами входа/выхода.
 *
 * frontend — открытое приложение (гейта нет): здесь достаточно подменить identityClass, чтобы
 * работали вход и личный кабинет.
 */
return [
    'app-backend' => [
        'components' => [
            'user' => [
                'identityClass' => Identity::class,
                'loginUrl' => '/User/backend/auth/login',
            ],
            'accessAuthorizer' => ['class' => RbacAuthorizer::class],
        ],
        'as access' => [
            'allowActions' => [
                'User/auth/login',
                'User/auth/logout',
            ],
        ],
    ],
    'app-rest' => [
        'components' => [
            'user' => [
                'identityClass' => Identity::class,
                'enableSession' => false,
            ],
            'accessAuthorizer' => ['class' => RbacAuthorizer::class],
        ],
    ],
    'app-frontend' => [
        'components' => [
            'user' => [
                'identityClass' => Identity::class,
            ],
        ],
    ],
];
