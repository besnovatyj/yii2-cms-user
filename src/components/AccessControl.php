<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\components;

use Besnovatyj\User\components\Helper;
use Yii;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\di\Instance;
use yii\web\ForbiddenHttpException;
use yii\web\User;

/**
 * Фильтр контроля доступа (ACF) — это простой метод авторизации,
 * который лучше всего использовать приложениям с простым контролем доступа.
 * Как следует из названия, ACF — это фильтр действий, который можно прикрепить к контроллеру
 * или модулю в качестве поведения. ACF проверит набор правил доступа, чтобы убедиться,
 * что текущий пользователь может получить доступ к запрошенному действию.
 *
 * Чтобы использовать AccessControl, объявите его в конфигурации приложения как поведение.
 * Например:
 * ```
 * 'as access' => [
 *     'class' => 'Besnovatyj\User\components\AccessControl',
 *     'allowActions' => ['site/login', 'site/error']
 * ]
 * ```
 * @property User $user
 */
class AccessControl extends ActionFilter
{
    /** @var array - Список действий, для доступа к которым не требуется проверка прав. */
    public array $allowActions = [];
    /** @var string|User - Пользователь, права доступа которого проверяются. */
    private string|User $_user = 'user';

    /**
     * @throws InvalidConfigException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action): bool
    {
        $actionId = $action->getUniqueId();
        $user = $this->getUser();
        if (Helper::checkRoute('/' . $actionId, Yii::$app->getRequest()->get(), $user)) {
            return true;
        }
        $this->denyAccess($user);
        return false;
    }

    /**
     * Get user
     * @return User
     * @throws InvalidConfigException
     */
    public function getUser(): User
    {
        if (!$this->_user instanceof User) {
            $this->_user = Instance::ensure($this->_user, User::class);
        }
        return $this->_user;
    }

    /**
     * Set user
     * @param string|User $user
     */
    public function setUser(User|string $user): void
    {
        $this->_user = $user;
    }

    /**
     * Запрещает доступ пользователя.
     * Реализация по умолчанию перенаправит пользователя на страницу входа, если он гость;
     * если пользователь уже вошел в систему, будет выдано исключение HTTP 403.
     *
     * @param User $user - Текущий пользователь.
     * @throws ForbiddenHttpException - если пользователь уже вошел в систему.
     */
    protected function denyAccess(User $user): void
    {
        if ($user->getIsGuest()) {
            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }


    protected function isActive($action): bool
    {
        $uniqueId = $action->getUniqueId();
        if ($uniqueId === Yii::$app->getErrorHandler()->errorAction) {
            return false;
        }

        $user = $this->getUser();
        if ($user->getIsGuest()) {
            $loginUrl = null;
            if (is_array($user->loginUrl) && isset($user->loginUrl[0])) {
                $loginUrl = $user->loginUrl[0];
            } else if (is_string($user->loginUrl)) {
                $loginUrl = $user->loginUrl;
            }
            if (!is_null($loginUrl) && trim((string)$loginUrl, '/') === $uniqueId) {
                return false;
            }
        }

        if ($this->owner instanceof Module) {
            // convert action uniqueId into an ID relative to the module
            $mid = $this->owner->getUniqueId();
            $id = $uniqueId;
            if ($mid !== '' && strpos($id, $mid . '/') === 0) {
                $id = substr($id, strlen($mid) + 1);
            }
        } else {
            $id = $action->id;
        }

        foreach ($this->allowActions as $route) {
            if (substr($route, -1) === '*') {
                $route = rtrim((string)$route, "*");
                if ($route === '' || strpos($id, $route) === 0) {
                    return false;
                }
            } else {
                if ($id === $route) {
                    return false;
                }
            }
        }

        if ($action->controller->hasMethod('allowAction') && in_array($action->id, $action->controller->allowAction())) {
            return false;
        }

        return true;
    }
}
