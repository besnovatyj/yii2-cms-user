<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

/**
 * Глобальные компоненты приложения от модуля пользователей (контракт ProvidesComponents).
 *
 * `authManager` переехал сюда из ядрового `common/config/components.php`: RBAC-таблицы принадлежат
 * этому модулю (их создают его миграции `user_auth_*`), поэтому без модуля ядру authManager не нужен —
 * гейт работает через контракт AccessAuthorizer, а не напрямую через authManager. Конфиг одинаков во
 * всех приложениях, поэтому это глобальный вклад, а не per-app (см. appConfig.php).
 */
return [
    'authManager' => [
        'class' => \yii\rbac\DbManager::class,
        'cache' => YII_DEBUG ? null : 'cache',
        'itemTable' => '{{%user_auth_items}}',
        'itemChildTable' => '{{%user_auth_item_children}}',
        'assignmentTable' => '{{%user_auth_assignments}}',
        'ruleTable' => '{{%user_auth_rules}}',
    ],
];
