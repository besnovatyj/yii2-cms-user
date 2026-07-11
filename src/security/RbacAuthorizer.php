<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\User\security;

use Besnovatyj\Contracts\security\AccessAuthorizer;
use Besnovatyj\User\components\Helper;

/**
 * RBAC-авторизатор модуля пользователей — «богатая» реализация {@see AccessAuthorizer}, которой
 * закрытые приложения перекрывают ядровый {@see \Besnovatyj\Kernel\security\DenyAllAuthorizer}.
 *
 * Тонкая обёртка над существующим {@see Helper::checkRoute()} — сохраняет прежнее поведение гейта
 * бэкенда 1:1 (strict/onlyRegisteredRoute, восхождение по префиксам `*`, defaultRoles). Регистрируется
 * как компонент `accessAuthorizer` через {@see \Besnovatyj\User\Module::appConfig()}.
 */
final class RbacAuthorizer implements AccessAuthorizer
{
    /**
     * @param int|null $userId не используется: {@see Helper::checkRoute()} в strict-режиме зовёт
     *   `$user->can()` и потому работает с ТЕКУЩИМ компонентом `user` (передаём null → берётся
     *   `Yii::$app->getUser()`), а не с голым id. Параметр оставлен для совместимости с контрактом.
     */
    public function isAllowed(string $route, array $params = [], ?int $userId = null): bool
    {
        return Helper::checkRoute($route, $params);
    }
}
