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
 *
 * URL-правила фронтенда (вход/регистрация/кабинет) — вклад в `frontendUrlManager` группы `common`
 * (см. README_Yii2_Modules.md). Перенесены из захардкоженного `frontend/config/url-manager.php`;
 * первый сегмент роута капитализирован под реальный id модуля 'User'. Гейтятся modman.
 */
return [
    'modules' => [
        Module::moduleId() => array_merge(
            ['class' => Module::class],
            Module::moduleConfig(),
            ['version' => Module::moduleVersion()],
        ),
    ],
    'components' => array_merge(Module::components(), [
        'frontendUrlManager' => [
            'rules' => [
                'signup'                                     => 'User/auth/signup/request',
                'pass-reset'                                 => 'User/auth/reset/request',
                'signup/<_a:[\w-]+>'                         => 'User/auth/signup/<_a>',
                'network/<_a:[\w-]+>'                        => 'User/auth/network/<_a>',
                '<_a:login|logout>'                          => 'User/auth/auth/<_a>',

                'cabinet'                                    => 'User/cabinet/default/index',
                'cabinet/<_c:[\w\-]+>'                       => 'User/cabinet/<_c>/index',
                'cabinet/<_c:[\w\-]+>/<id:\d+>'              => 'User/cabinet/<_c>/view',
                'cabinet/<_c:[\w\-]+>/<_a:[\w-]+>'           => 'User/cabinet/<_c>/<_a>',
                'cabinet/<_c:[\w\-]+>/<id:\d+>/<_a:[\w\-]+>' => 'User/cabinet/<_c>/<_a>',
            ],
        ],
    ]),
];
