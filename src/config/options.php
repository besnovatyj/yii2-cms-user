<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

// Все опции должны быть изначально определены при в конфигурации модуля при подключении в приложение.
return [
    'user_password_reset_token_expire' => [
        'path' => 'modules.User.params.passwordResetTokenExpire',
        'label' => 'Время жизни токена для сброса пароля',
        'description' => 'Время (в секундах), по истечении которого необходимо повторно запросить токен для сброса пароля пользователя.',
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
        'label' => 'Продолжительность действия функции «Запомнить меня»',
        'description' => 'Время (в секундах), по истечении которого требуется повторная авторизация.',
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
        'label' => 'Время жизни кеша, сек.',
        'description' => 'Сколько секунд живут кеши тяжёлых операций модуля: полный список маршрутов приложения '
            . '(сканирование контроллёров всех модулей — справочник для выдачи прав) и списки маршрутов, '
            . 'разрешённых пользователю и ролям по умолчанию. Вторые участвуют в проверке доступа, но задержки '
            . 'в правах не создают: кеш прав сбрасывается сразу при изменении ролей, разрешений, их иерархии, '
            . 'назначений и состава маршрутов. Срок ограничивает лишь правки в обход админки — например, прямо '
            . 'в БД. Задействован при выключенном <code>strict</code>: со включённым проверка идёт напрямую '
            . 'через RBAC со своим кешем. <b>0 — кеш бессрочный</b>, до ручного сброса.',
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
    'user_defaultUserStatus' => [
        'path' => 'modules.User.params.defaultUserStatus',
        'label' => 'Дефолтный статус активности пользователя',
        'description' => 'Статус, в котором будет пользователь после его создания',
        'group' => '',
        'category' => 'User',
        'rules' => [
            ['required'],
            ['integer'],
        ],
        'inputOptions' => [
            'type' => 'dropdown',
            'items' => [
                '0' => 'Ожидает',
                '10' => 'Активный',
            ],
        ],
    ],
    'user_onlyRegisteredRoute' => [
        'path' => 'modules.User.params.onlyRegisteredRoute',
        'label' => 'Если true, AccessControl проверяет только зарегистрированные маршруты',
        'description' => 'Если true, то проверяет только те маршруты, которые добавлены в модуле вручную. Не добавленные маршруты разрешены всем',
        'group' => '',
        'category' => 'User',
        'rules' => [
            ['required'],
            ['boolean'],
        ],
        'inputOptions' => [
            'type' => 'dropdown',
            'items' => [
                0 => 'False',
                1 => 'True',
            ],
        ],
    ],
    'user_strict' => [
        'path' => 'modules.User.params.strict',
        'label' => 'Если false, AccessControl выполнит проверку без учета Rules',
        'description' => 'Если false, `Rules (в виде php кода)` не проверяются и не применяются',
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
        'label' => 'Тег кеша',
        'description' => 'Тег кеша, но не на всё, надо смотреть код',
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
