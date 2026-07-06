<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\manage;

use DomainException;
use Besnovatyj\User\components\Rbac;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\backend\PasswordEditForm;
use Besnovatyj\User\forms\backend\UserCreateForm;
use Besnovatyj\User\forms\backend\UserEditForm;
use Besnovatyj\User\repositories\UserRepository;
use Throwable;
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

        $this->repository->save($user);
        $this->roles->assign($user->id, $form->role);

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
        // TODO - удалять профайл при удалении юзера
        // TODO - Обернуть всё в транзакцию
        $this->revokeAllRoles($id);
        $this->repository->remove($user);
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
