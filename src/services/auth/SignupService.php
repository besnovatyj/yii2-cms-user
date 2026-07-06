<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\auth;

use DomainException;
use Besnovatyj\User\components\Rbac;
use Besnovatyj\User\entities\Profile;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\frontend\SignupForm;
use Besnovatyj\User\repositories\UserRepository;
use Besnovatyj\User\services\manage\RoleManager;
use Throwable;
use Yii;

class SignupService
{
    private UserRepository $users;
    private RoleManager $roles;

    public function __construct(
        UserRepository $users,
        RoleManager    $roles
    ) {
        $this->users = $users;
        $this->roles = $roles;
    }

    /**
     * @throws \yii\base\Exception
     * @throws Throwable
     */
    public function signup(SignupForm $form): User
    {
        $user = User::signupRequest(
            $form->username,
            $form->email,
            $form->phone,
            $form->password
        );

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->users->save($user);

            $this->saveProfile(
                $user->id,
                $form->profile->sex,
                $form->profile->firstName,
                $form->profile->lastName
            );

            $transaction->commit();
            return $user;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws \yii\db\Exception
     * @throws Throwable
     */
    public function confirm(string $token): void
    {
        if (empty($token)) {
            throw new DomainException('Empty confirm token.');
        }

        $user = $this->users->getByEmailConfirmToken($token);
        $user->signupConfirm();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->users->save($user);
            $this->roles->assign($user->id, Rbac::ROLE_USER);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // ==================== Private methods ====================

    /**
     * @throws \yii\db\Exception
     */
    private function saveProfile(int $userId, $sex, $firstName, $lastName): void
    {
        $profile = Profile::create($userId, $sex, $firstName, $lastName);

        if (!$profile->save()) {
            throw new \yii\db\Exception('Failed to save profile.');
        }
    }
}
