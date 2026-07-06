<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\cabinet;

use DomainException;
use Besnovatyj\User\entities\Profile;
use Besnovatyj\User\forms\frontend\UserEditForm;
use Besnovatyj\User\repositories\UserRepository;
use Throwable;
use Yii;

class ProfileService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Редактирует профиль пользователя.
     * Возвращает true если был изменён email и отправлен запрос на подтверждение.
     * @throws \yii\base\Exception
     * @throws Throwable
     */
    public function edit(int $id, UserEditForm $form): bool
    {
        $user = $this->users->get($id);
        $emailChanged = $user->editByUser(
            $form->username,
            $form->email,
            $form->phone,
        );

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->users->save($user);

            $this->saveProfile(
                $user->id,
                $form->profile->sex,
                $form->profile->firstName,
                $form->profile->lastName,
                $form->profile->photo
            );

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        return $emailChanged;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function confirmPhoneChange(int $id, string $token): void
    {
        $user = $this->users->get($id);
        $result = $user->confirmPhoneChange($token);
        $this->users->save($user);
        if (!$result) {
            throw new DomainException('Ошибка подтверждения.');
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function confirmEmail(string $token): void
    {
        if (empty($token)) {
            throw new DomainException('Неверный токен подтверждения.');
        }
        $user = $this->users->getByEmailConfirmToken($token);
        $user->confirmEmail();
        $this->users->save($user);
    }

    // ==================== Private methods ====================

    /**
     * @throws \yii\db\Exception
     */
    private function saveProfile(int $userId, $sex, $firstName, $lastName, $photo = null): void
    {
        $profile = Profile::findOne(['user_id' => $userId]);

        if ($profile) {
            $profile->edit($sex, $firstName, $lastName);
        } else {
            $profile = Profile::create($userId, $sex, $firstName, $lastName);
        }

        if ($photo) {
            $profile->setPhoto($photo);
        }

        if (!$profile->save()) {
            throw new \yii\db\Exception('Failed to save profile.');
        }
    }
}
