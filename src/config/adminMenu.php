<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

return [
    // Users list
    [
        'label' => 'Users list',
        'iconClass' => 'bi bi-list-ol me-1',
        'url' => ['/user/backend/user/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'user/backend/user');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'right-sidebar',
                    'group' => 'Users',
                    'groupIcon' => 'bi bi-people',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Routes
    [
        'label' => 'Routes',
        'iconClass' => 'bi bi-sign-turn-right me-1',
        'url' => ['/user/backend/route/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'user/backend/route');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'right-sidebar',
                    'group' => 'Users',
                    'groupIcon' => 'bi bi-people',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Permissions
    [
        'label' => 'Permissions',
        'iconClass' => 'bi bi-check2-all me-1',
        'url' => ['/user/backend/permission/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'user/backend/permission');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'right-sidebar',
                    'group' => 'Users',
                    'groupIcon' => 'bi bi-people',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Roles
    [
        'label' => 'Roles',
        'iconClass' => 'bi bi-person-gear me-1',
        'url' => ['/user/backend/role/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'user/backend/role');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'right-sidebar',
                    'group' => 'Users',
                    'groupIcon' => 'bi bi-people',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Rules
    [
        'label' => 'Rules',
        'iconClass' => 'bi bi-filetype-php me-1',
        'url' => ['/user/backend/rule/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'user/backend/rule');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'right-sidebar',
                    'group' => 'Users',
                    'groupIcon' => 'bi bi-people',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],

    // Assignments
    [
        'label' => 'Assignments',
        'iconClass' => 'bi bi-arrows me-1',
        'url' => ['/user/backend/assignment/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'user/backend/assignment');
        },
        '_meta' => [
            'placements' => [
                [
                    'location' => 'right-sidebar',
                    'group' => 'Users',
                    'groupIcon' => 'bi bi-people',
                    'priority' => 100,
                    'groupPriority' => 100,
                ],
            ],
        ],
    ],
];
