<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

// Все опции должны быть изначально определены при в конфигурации модуля при подключении в приложение.
return [
    'user_password_reset_token_expire' => [
        'path' => 'modules.user.params.passwordResetTokenExpire',
        'label' => 'User password reset token expire',
        'description' => 'The time (in seconds) after which it is necessary to re-request the token to reset the user password. (\Yii::$app->getModule(\'user\')->params[\'passwordResetTokenExpire\'])',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_rememberMeDuration' => [
        'path' => 'modules.user.params.rememberMeDuration',
        'label' => 'Remember me duration',
        'description' => 'Time (in seconds) after which it is necessary to re-authorize. (Yii::$app->getModule(\'user\')->params[\'rememberMeDuration\'])',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_cache_duration' => [
        'path' => 'modules.user.params.cacheDuration',
        'label' => 'Время жизни кеша для тяжелых операций (Список всех маршрутов приложения через сканирование всех контроллёров всех модулей)',
        'description' => 'Yii::$app->getModule(\'user\')->params[\'cacheDuration\']',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_userTable' => [
        'path' => 'modules.user.params.userTable',
        'label' => 'Users table',
        'description' => 'Yii::$app->getModule(\'user\')->params[\'userTable\']',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['string'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_defaultUserStatus' => [
        'path' => 'modules.user.params.defaultUserStatus',
        'label' => 'Default User Status',
        'description' => 'Yii::$app->getModule(\'user\')->params[\'defaultUserStatus\']',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'dropdown',
            'items' => [
                '0' => 'Pending',
                '10' => 'Active',
            ],
        ],
    ],
    'user_userRolePageSize' => [
        'path' => 'modules.user.params.userRolePageSize',
        'label' => 'Size of user roles page',
        'description' => 'Yii::$app->getModule(\'user\')->params[\'userRolePageSize\']',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_onlyRegisteredRoute' => [
        'path' => 'modules.user.params.onlyRegisteredRoute',
        'label' => 'If true then AccessControl only check if route are registered',
        'description' => 'Yii::$app->getModule(\'user\')->params[\'onlyRegisteredRoute\']',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['boolean'],
        ],
        'inputOptions' => [
            'type' => 'dropdown',
            'items' => [
                1 => 'True',
                0 => 'False',
            ],
        ],
    ],
    'user_strict' => [
        'path' => 'modules.user.params.strict',
        'label' => 'If false then AccessControl will check without Rule',
        'description' => 'Yii::$app->getModule(\'user\')->params[\'strict\']',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['boolean'],
        ],
        'inputOptions' => [
            'type' => 'dropdown',
            'items' => [
                1 => 'True',
                0 => 'False',
            ],
        ],
    ],
    'user_globalCacheTag' => [
        'path' => 'modules.user.params.globalCacheTag',
        'label' => 'globalCacheTag',
        'description' => 'Yii::$app->getModule(\'user\')->params[\'globalCacheTag\']',
        'group' => '',
        'category' => 'user',
        'rules' => [
            ['required'],
            ['string'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
];
