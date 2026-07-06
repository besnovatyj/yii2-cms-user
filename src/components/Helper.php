<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\components;

use Exception;
use Yii;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\web\User;

class Helper
{
    private static array $_userRoutes = [];
    private static $_defaultRoutes;
    private static $_routes;

    /**
     * Filter menu items
     * @param array $items
     * @param null $user
     * @return array
     * @throws Exception
     */
    public static function filter(array $items, $user = null): array
    {
        if ($user === null) {
            $user = Yii::$app->getUser();
        }
        return static::filterRecursive($items, $user);
    }

    /**
     * Filter menu recursive
     * @param array $items
     * @param integer|User $user
     * @return array
     * @throws Exception
     */
    protected static function filterRecursive(array $items, User|int $user): array
    {
        $result = [];
        foreach ($items as $i => $item) {
            $url = ArrayHelper::getValue($item, 'url', '#');
            $allow = is_array($url) ? static::checkRoute($url[0], array_slice($url, 1), $user) : true;

            if (isset($item['items']) && is_array($item['items'])) {
                $subItems = self::filterRecursive($item['items'], $user);
                if (count($subItems)) {
                    $allow = true;
                }
                $item['items'] = $subItems;
            }
            if ($allow && !($url == '#' && empty($item['items']))) {
                $result[$i] = $item;
            }
        }
        return $result;
    }

    /**
     * Check access route for user.
     * @param array|string $route
     * @param array $params
     * @param null $user
     * @return boolean
     */
    public static function checkRoute(array|string $route, array $params = [], $user = null): bool
    {
        $r = static::normalizeRoute($route);

        if (Yii::$app->getModule('User')->params['onlyRegisteredRoute'] && !isset(static::getRegisteredRoutes()[$r])) {
            return true;
        }

        if ($user === null) {
            $user = Yii::$app->getUser();
        }
        $userId = $user instanceof User ? $user->getId() : $user;

        if (Yii::$app->getModule('User')->params['strict']) {
            if ($user->can($r, $params)) {
                return true;
            }
            while (($pos = strrpos($r, '/')) > 0) {
                $r = substr($r, 0, $pos);
                if ($user->can($r . '/*', $params)) {
                    return true;
                }
            }
            return $user->can('/*', $params);
        } else {
            $routes = static::getRoutesByUser($userId);
            if (isset($routes[$r])) {
                return true;
            }
            while (($pos = strrpos($r, '/')) > 0) {
                $r = substr($r, 0, $pos);
                if (isset($routes[$r . '/*'])) {
                    return true;
                }
            }
            return isset($routes['/*']);
        }
    }

    /**
     * Normalize route
     * @param string $route Plain route string
     * @return string Normalized route string
     */
    protected static function normalizeRoute(string $route): string
    {
        if ($route === '') {
            $normalized = '/' . Yii::$app->controller->getRoute();
        } elseif (strncmp($route, '/', 1) === 0) {
            $normalized = $route;
        } elseif (!str_contains($route, '/')) {
            $normalized = '/' . Yii::$app->controller->getUniqueId() . '/' . $route;
        } elseif (($mid = Yii::$app->controller->module->getUniqueId()) !== '') {
            $normalized = '/' . $mid . '/' . $route;
        } else {
            $normalized = '/' . $route;
        }
        return $normalized;
    }

    public static function getRegisteredRoutes(): array
    {
        if (self::$_routes === null) {
            self::$_routes = [];
            $manager = Yii::$app->getAuthManager();
            foreach ($manager->getPermissions() as $item) {
                if ($item->name[0] === '/') {
                    self::$_routes[$item->name] = $item->name;
                }
            }
        }
        return self::$_routes;
    }

    /**
     * Get assigned routes of user.
     * @param integer $userId
     * @return array
     */
    public static function getRoutesByUser(int $userId): array
    {
        if (!isset(self::$_userRoutes[$userId])) {
            $cache = Yii::$app->getCache();
            if ($cache && ($routes = $cache->get([__METHOD__, $userId])) !== false) {
                self::$_userRoutes[$userId] = $routes;
            } else {
                $routes = static::getDefaultRoutes();
                $manager = Yii::$app->getAuthManager();
                foreach ($manager->getPermissionsByUser($userId) as $item) {
                    if ($item->name[0] === '/') {
                        $routes[$item->name] = true;
                    }
                }
                self::$_userRoutes[$userId] = $routes;
                if ($cache) {
                    $cache->set([__METHOD__, $userId], $routes, Yii::$app->getModule('User')->params['cacheDuration'], new TagDependency([
                        'tags' => Yii::$app->getModule('User')->params['globalCacheTag'],
                    ]));
                }
            }
        }
        return self::$_userRoutes[$userId];
    }

    /**
     * Get assigned routes by default roles
     * @return array
     */
    protected static function getDefaultRoutes(): array
    {
        if (self::$_defaultRoutes === null) {
            $manager = Yii::$app->getAuthManager();
            $roles = $manager->defaultRoles;
            $cache = Yii::$app->getCache();
            if ($cache && ($routes = $cache->get($roles)) !== false) {
                self::$_defaultRoutes = $routes;
            } else {
                $permissions = self::$_defaultRoutes = [];
                foreach ($roles as $role) {
                    $permissions = array_merge($permissions, $manager->getPermissionsByRole($role));
                }
                foreach ($permissions as $item) {
                    if ($item->name[0] === '/') {
                        self::$_defaultRoutes[$item->name] = true;
                    }
                }
                if ($cache) {
                    $cache->set($roles, self::$_defaultRoutes, Yii::$app->getModule('User')->params['cacheDuration'], new TagDependency([
                        'tags' => Yii::$app->getModule('User')->params['globalCacheTag'],
                    ]));
                }
            }
        }
        return self::$_defaultRoutes;
    }

    /**
     * Filter action column button. Use with [[yii\grid\GridView]]
     * ```php
     * 'columns' => [
     *     ...
     *     [
     *         'class' => 'yii\grid\ActionColumn',
     *         'template' => Helper::filterActionColumn(['view','update','activate'])
     *     ]
     * ],
     * ```
     * @param array|string $buttons
     * @param integer|User $user
     * @return string
     */
    public static function filterActionColumn($buttons = [], $user = null): string
    {
        // Если передаём массив кнопок // $buttons = ['install', 'config', 'delete'];
        if (is_array($buttons)) {
            $result = [];
            foreach ($buttons as $button) {
                if (static::checkRoute($button, [], $user)) {
                    $result[] = "{{$button}}";
                }
            }
            return implode(' ', $result);
        }

        // Если передаём кнопки в строке // $buttons = '{view} {update} {delete}';
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($user) {
            return static::checkRoute($matches[1], [], $user) ? "{{$matches[1]}}" : '';
        }, $buttons);
    }

    /**
     * Use to invalidate cache.
     */
    public static function invalidate(): void
    {
        if (Yii::$app->getCache() !== null) {
            TagDependency::invalidate(Yii::$app->getCache(), Yii::$app->getModule('User')->params['globalCacheTag']);
        }
    }
}
