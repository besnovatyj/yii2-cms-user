<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\auth;

use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\LoginForm;
use Besnovatyj\User\repositories\UserRepository;
use Yii;

class AuthService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    public function auth(LoginForm $form): User
    {
        $request = Yii::$app->getRequest();
        $ip = $request instanceof \yii\web\Request ? ($request->getUserIP() ?? 'unknown') : 'cli';

        $user = $this->users->findActiveByUsernameOrEmail($form->username);
        if (!$user || !$user->validatePassword($form->password)) {

            // TODO - Задокументировать на будущее в глобальном readme.md, чтобы не забыть.
            //  Не убирать, используется file2ban именно в этом формате, а не просто так тут ругается.
            Yii::warning("Failed login from {$ip} for {$form->username}", 'auth/login');

            throw new \DomainException('Неизвестный пользователь или пароль.');
        }

        Yii::info("Successful login from {$ip} for {$user->username}", 'auth/login');
        return $user;
    }
}
