<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\manage;

use Besnovatyj\User\components\Helper;
use Exception;
use ReflectionClass;
use Yii;
use yii\base\Controller;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\caching\CacheInterface;
use yii\helpers\VarDumper;
use yii\rbac\ManagerInterface;

class RouteManageService
{
    protected ManagerInterface $authManager;
    protected CacheInterface|null $cache;

    public function __construct(
        ManagerInterface $authManager,
    )
    {
        $this->authManager = $authManager;
        $this->cache = Yii::$app->cache;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getRoutes(bool $withoutCache = false): array
    {
        $routes = $this->getAppRoutes($withoutCache);
        $exists = [];
        foreach (array_keys($this->authManager->getPermissions()) as $name) {
            if ($name[0] !== '/') { // всё что начинается не со слеша, то Route, остальное - Permissions
                continue;
            }
            $exists[] = $name;
            unset($routes[$name]);
        }
        return [
            'available' => array_keys($routes),
            'assigned' => $exists,
        ];
    }

    /**
     * Create new routes
     * @throws Exception
     */
    public function create(string $routes): array
    {
        $routes = preg_split('/\s*,\s*/', trim((string)$routes), -1, PREG_SPLIT_NO_EMPTY);
        return $this->assign($routes);
    }

    /**
     * Add existed routes
     * @throws Exception
     */
    public function assign(array $routes): array
    {
        $this->addNew($routes);
        return $this->getRoutes(true);
    }

    /**
     * Remove routes
     * @param array $routes
     * @return array
     * @throws Exception
     */
    public function remove(array $routes): array
    {
        foreach ($routes as $route) {
            $item = $this->authManager->createPermission($route);
            $this->authManager->remove($item);
        }
        // Состав маршрутов входит в кешированные списки прав пользователей (Helper::getRoutesByUser)
        Helper::invalidate();

        return $this->getRoutes(true);
    }

    /**
     * Insert new routes in db
     * @param array $routes
     * @return void
     * @throws Exception
     */
    public function addNew(array $routes): void
    {
        /**
         * Добавляет Permission начинающийся со слеша,
         * что в данной системе управления воспринимается как Route, а не Permission.
         */
        foreach ($routes as $route) {
            if (!str_starts_with($route, '/')) {
                $route = '/' . $route;
            }
            $item = $this->authManager->createPermission($route);
            $this->authManager->add($item);
        }
        // Регистрация маршрута меняет права: снимаем кешированные списки, как это делают
        // остальные операции RBAC (см. AuthItem::save(), AssignmentManageService)
        Helper::invalidate();
    }

    /**
     * Get list of real app routes
     * @param bool $withoutCache
     * @return array
     * @throws InvalidConfigException
     */
    public function getAppRoutes(bool $withoutCache = false): array
    {
        $key = 'user_app_routes';
        $result = [];
        if ($this->cache !== null && !$withoutCache) {
            return $this->cache->getOrSet($key, function () use ($result) {
                $this->getRouteRecursive(Yii::$app, $result);
                return $result;
            }, Yii::$app->getModule('User')->params['cacheDuration']);
        }
        $this->cache->delete($key);
        $this->getRouteRecursive(Yii::$app, $result);
        return $result;
    }

    /**
     * Get route(s) recursive
     * @param Module $module
     * @param array $result
     * @throws InvalidConfigException
     */
    protected function getRouteRecursive(Module $module, array &$result): void
    {
        $token = "Get Route of '" . get_class($module) . "' with id '" . $module->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);

        foreach ($module->getModules() as $id => $child) {
            if (($child = $module->getModule($id)) !== null) {
                $this->getRouteRecursive($child, $result);
            }
        }

        foreach ($module->controllerMap as $id => $type) {
            $this->getControllerActions($type, $id, $module, $result);
        }

        $namespace = trim((string)$module->controllerNamespace, '\\') . '\\';
        $this->getControllerFiles($module, $namespace, '', $result);
        $all = '/' . ltrim((string)$module->uniqueId . '/*', '/');
        $result[$all] = $all;

        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list action of controller
     * @param array|string $type
     * @param string $id
     * @param Module $module
     * @param array $result
     */
    protected function getControllerActions(array|string $type, string $id, Module $module, array &$result): void
    {
        $token = "Create controller with config=" . VarDumper::dumpAsString($type) . " and id='$id'";
        Yii::beginProfile($token, __METHOD__);
        try { // Обёрнуто чтобы выводился список при ошибках создания объектов, а не вылетало вообще всё
            /* @var $controller Controller */
            $controller = Yii::createObject($type, [$id, $module]);
            $this->getActionRoutes($controller, $result);
            $all = "/{$controller->uniqueId}/*";
            $result[$all] = $all;
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get route of action
     * @param Controller $controller
     * @param array $result all controller action.
     */
    protected function getActionRoutes(Controller $controller, array &$result): void
    {
        $token = "Get actions of controller '" . $controller->uniqueId . "'";
        Yii::beginProfile($token, __METHOD__);

        $prefix = '/' . $controller->uniqueId . '/';
        foreach ($controller->actions() as $id => $value) {
            $result[$prefix . $id] = $prefix . $id;
        }
        $class = new ReflectionClass($controller);
        foreach ($class->getMethods() as $method) {
            $name = $method->getName();
            if ($method->isPublic() && !$method->isStatic() && str_starts_with($name, 'action') && $name !== 'actions') {
                $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', substr($name, 6)));
                $id = $prefix . ltrim((string)str_replace(' ', '-', $name), '-');
                $result[$id] = $id;
            }
        }

        Yii::endProfile($token, __METHOD__);
    }

    /**
     * Get list controller under module
     * @param Module $module
     * @param string $namespace
     * @param string $prefix
     * @param array $result
     * @throws InvalidConfigException
     */
    protected function getControllerFiles(Module $module, string $namespace, string $prefix, array &$result): void
    {
        $path = Yii::getAlias('@' . str_replace('\\', '/', $namespace), false);
        $token = "Get controllers from '$path'";
        Yii::beginProfile($token, __METHOD__);

        if (!is_dir($path)) {
            return;
        }
        foreach (scandir($path) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_dir($path . '/' . $file) && preg_match('%^[a-z0-9_/]+$%i', $file . '/')) {
                $this->getControllerFiles($module, $namespace . $file . '\\', $prefix . $file . '/', $result);
            } elseif (strcmp(substr($file, -14), 'Controller.php') === 0) {
                $baseName = substr(basename($file), 0, -14);
                $name = strtolower(preg_replace('/(?<![A-Z])[A-Z]/', ' \0', $baseName));
                $id = ltrim((string)str_replace(' ', '-', $name), '-');
                $className = $namespace . $baseName . 'Controller';
                if (!str_contains($className, '-') && class_exists($className) && is_subclass_of($className, Controller::class)) {
                    $this->getControllerActions($className, $prefix . $id, $module, $result);
                }
            }
        }

        Yii::endProfile($token, __METHOD__);
    }

}
