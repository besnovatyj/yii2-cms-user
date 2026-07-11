<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\User\Module;

/**
 * Yii2-конфиг модуля для движка yiisoft/config (группа `common` — общий для всех приложений).
 *
 * Регистрация модуля + глобальные компоненты (authManager — RBAC-таблицы принадлежат этому модулю).
 * Пер-аппликационные вклады (identityClass, accessAuthorizer, whitelist входа) — в config/{app}.php.
 * Меню/миграции остаются вкладами modman. Значения — из статических методов {@see Module}.
 */
return [
    'modules' => [
        Module::moduleId() => array_merge(
            ['class' => Module::class],
            Module::moduleConfig(),
            ['version' => Module::moduleVersion()],
        ),
    ],
    'components' => Module::components(),
];
