<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Contracts\adminMenu\AdminMenuLocation;
use Besnovatyj\Contracts\adminMenu\AdminMenuPlacement;

return [
    // Users list
    [
        'label' => 'Users list',
        'iconClass' => 'bi bi-list-ol me-1',
        'url' => ['/User/backend/user/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'User/backend/user');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::RightSidebar,
                    group: 'Users',
                    groupIcon: 'bi bi-people',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],

    // Routes
    [
        'label' => 'Routes',
        'iconClass' => 'bi bi-sign-turn-right me-1',
        'url' => ['/User/backend/route/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'User/backend/route');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::RightSidebar,
                    group: 'Users',
                    groupIcon: 'bi bi-people',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],

    // Permissions
    [
        'label' => 'Permissions',
        'iconClass' => 'bi bi-check2-all me-1',
        'url' => ['/User/backend/permission/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'User/backend/permission');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::RightSidebar,
                    group: 'Users',
                    groupIcon: 'bi bi-people',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],

    // Roles
    [
        'label' => 'Roles',
        'iconClass' => 'bi bi-person-gear me-1',
        'url' => ['/User/backend/role/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'User/backend/role');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::RightSidebar,
                    group: 'Users',
                    groupIcon: 'bi bi-people',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],

    // Rules
    [
        'label' => 'Rules',
        'iconClass' => 'bi bi-filetype-php me-1',
        'url' => ['/User/backend/rule/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'User/backend/rule');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::RightSidebar,
                    group: 'Users',
                    groupIcon: 'bi bi-people',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],

    // Assignments
    [
        'label' => 'Assignments',
        'iconClass' => 'bi bi-arrows me-1',
        'url' => ['/User/backend/assignment/index'],
        'active' => static function () {
            return str_contains(Yii::$app->request->url, 'User/backend/assignment');
        },
        '_meta' => [
            'placements' => [
                new AdminMenuPlacement(
                    location: AdminMenuLocation::RightSidebar,
                    group: 'Users',
                    groupIcon: 'bi bi-people',
                    groupPriority: 100,
                    priority: 100,
                ),
            ],
        ],
    ],
];
