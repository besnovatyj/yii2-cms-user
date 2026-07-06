<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

// Все опции должны быть изначально определены при в конфигурации модуля при подключении в приложение.
return [
    'user_password_reset_token_expire' => [
        'path' => 'modules.User.params.passwordResetTokenExpire',
        'label' => 'User password reset token expire',
        'description' => 'The time (in seconds) after which it is necessary to re-request the token to reset the user password. (\Yii::$app->getModule(\'User\')->params[\'passwordResetTokenExpire\'])',
        'group' => '',
        'category' => 'User',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_rememberMeDuration' => [
        'path' => 'modules.User.params.rememberMeDuration',
        'label' => 'Remember me duration',
        'description' => 'Time (in seconds) after which it is necessary to re-authorize. (Yii::$app->getModule(\'User\')->params[\'rememberMeDuration\'])',
        'group' => '',
        'category' => 'User',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_cache_duration' => [
        'path' => 'modules.User.params.cacheDuration',
        'label' => 'Время жизни кеша для тяжелых операций (Список всех маршрутов приложения через сканирование всех контроллёров всех модулей)',
        'description' => 'Yii::$app->getModule(\'User\')->params[\'cacheDuration\']',
        'group' => '',
        'category' => 'User',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_userTable' => [
        'path' => 'modules.User.params.userTable',
        'label' => 'Users table',
        'description' => 'Yii::$app->getModule(\'User\')->params[\'userTable\']',
        'group' => '',
        'category' => 'User',
        'rules' => [
            ['required'],
            ['string'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_defaultUserStatus' => [
        'path' => 'modules.User.params.defaultUserStatus',
        'label' => 'Default User Status',
        'description' => 'Yii::$app->getModule(\'User\')->params[\'defaultUserStatus\']',
        'group' => '',
        'category' => 'User',
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
        'path' => 'modules.User.params.userRolePageSize',
        'label' => 'Size of user roles page',
        'description' => 'Yii::$app->getModule(\'User\')->params[\'userRolePageSize\']',
        'group' => '',
        'category' => 'User',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
    'user_onlyRegisteredRoute' => [
        'path' => 'modules.User.params.onlyRegisteredRoute',
        'label' => 'If true then AccessControl only check if route are registered',
        'description' => 'Yii::$app->getModule(\'User\')->params[\'onlyRegisteredRoute\']',
        'group' => '',
        'category' => 'User',
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
        'path' => 'modules.User.params.strict',
        'label' => 'If false then AccessControl will check without Rule',
        'description' => 'Yii::$app->getModule(\'User\')->params[\'strict\']',
        'group' => '',
        'category' => 'User',
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
        'path' => 'modules.User.params.globalCacheTag',
        'label' => 'globalCacheTag',
        'description' => 'Yii::$app->getModule(\'User\')->params[\'globalCacheTag\']',
        'group' => '',
        'category' => 'User',
        'rules' => [
            ['required'],
            ['string'],
        ],
        'inputOptions' => [
            'type' => 'input',
        ],
    ],
];
