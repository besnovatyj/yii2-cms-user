<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\auth;

use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\frontend\PasswordResetRequestForm;
use Besnovatyj\User\forms\frontend\ResetPasswordForm;
use Besnovatyj\User\repositories\UserRepository;

class PasswordResetService
{
    private UserRepository $users;

    public function __construct(UserRepository $users,)
    {
        $this->users = $users;
    }

    /**
     * @throws \yii\base\Exception
     */
    public function request(PasswordResetRequestForm $form): void
    {
        $user = $this->users->getByEmail($form->email);
        $user->requestPasswordReset();
        $this->users->save($user);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function reset(string $token, ResetPasswordForm $form): void
    {
        $user = $this->users->getActiveByPasswordResetToken($token);
        $user->resetPassword($form->password);
        $this->users->save($user);
    }

    /**
     * Проверяет есть ли активный пользователь с таким токеном сброса пароля
     */
    public function checkToken(string $token): ?User
    {
        return $this->users->findActiveByPasswordResetToken($token);
    }
}
