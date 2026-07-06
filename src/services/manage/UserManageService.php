<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\manage;

use DomainException;
use Besnovatyj\User\components\Rbac;
use Besnovatyj\User\entities\Profile;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\backend\PasswordEditForm;
use Besnovatyj\User\forms\backend\ProfileForm;
use Besnovatyj\User\forms\backend\UserCreateForm;
use Besnovatyj\User\forms\backend\UserEditForm;
use Besnovatyj\User\repositories\UserRepository;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\db\StaleObjectException;

class UserManageService
{
    private UserRepository $repository;
    private RoleManager $roles;

    public function __construct(UserRepository $repository, RoleManager $roles)
    {
        $this->repository = $repository;
        $this->roles = $roles;
    }

    /**
     * @throws Exception
     */
    public function create(UserCreateForm $form): User
    {
        $user = User::create(
            $form->username,
            $form->email,
            $form->phone,
            $form->description,
            !empty($form->password) ? $form->password : \Yii::$app->security->generateRandomString(),
        );

        $this->repository->save($user);
        $this->roles->assign($user->id, $form->role);

        return $user;
    }

    /**
     * @throws Throwable
     * @throws \yii\db\Exception
     */
    public function edit($id, UserEditForm $form): void
    {
        $user = $this->repository->get($id);
        $user->edit(
            $form->username,
            $form->email,
            $form->phone,
            $form->description
        );

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->repository->save($user);
            $this->roles->assign($user->id, $form->role);
            $this->saveProfile($user->id, $form->profile);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Создаёт или обновляет профиль пользователя, включая загрузку фото.
     * @throws \yii\db\Exception
     */
    private function saveProfile(int $userId, ProfileForm $form): void
    {
        $profile = Profile::findOne(['user_id' => $userId]);

        if ($profile) {
            $profile->edit($form->sex, $form->firstName, $form->lastName);
        } else {
            $profile = Profile::create($userId, $form->sex, $form->firstName, $form->lastName);
        }

        if ($form->photo) {
            $profile->setPhoto($form->photo);
        }

        if (!$profile->save()) {
            throw new \yii\db\Exception('Ошибка сохранения профиля.');
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function passwordEdit($id, PasswordEditForm $form): void
    {
        $user = $this->repository->get($id);
        $user->passwordUpdate(
            $form->password
        );
        $this->repository->save($user);
    }

    public function assignRole($id, $role): void
    {
        $user = $this->repository->get($id);
        $this->roles->assign($user->id, $role);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function activate($id): void
    {
        $user = $this->repository->get($id);
        $user->activate();
        $this->roles->assign($user->id, Rbac::ROLE_USER);
        $this->repository->save($user);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function draft($id): void
    {
        $user = $this->repository->get($id);
        $user->draft();
        $this->repository->save($user);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function block($id): void
    {
        $user = $this->repository->get($id);
        if ($user->isRoot()) {
            throw new DomainException('Нельзя заблокировать root-пользователя.');
        }
        $user->block();
        $this->revokeAllRoles($id);
        $this->repository->save($user);
    }

    public function revokeAllRoles($id): void
    {
        $user = $this->repository->get($id);
        $this->roles->revokeAll($user->id);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove($id): void
    {
        $user = $this->repository->get($id);
        if ($user->isRoot()) {
            throw new DomainException('Нельзя удалить root-пользователя.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->revokeAllRoles($id);
            $this->removeProfile($id);
            $this->repository->remove($user);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * Удаляет профиль пользователя вместе с загруженными файлами.
     * Удаление через delete() запускает UploadBehavior::beforeDelete и чистит фото/превью.
     * @throws Throwable
     * @throws StaleObjectException
     */
    private function removeProfile(int $userId): void
    {
        $profile = Profile::findOne(['user_id' => $userId]);
        $profile?->delete();
    }

    /**
     * @throws \yii\db\Exception
     */
    public function resetEmailConfirmToken(int $id): void
    {
        $user = $this->repository->get($id);
        $user->resetEmailConfirmToken();
        $this->repository->save($user);
    }

    /**
     * @throws \yii\db\Exception
     */
    public function resetPasswordResetToken(int $id): void
    {
        $user = $this->repository->get($id);
        $user->resetPasswordResetToken();
        $this->repository->save($user);
    }

}
