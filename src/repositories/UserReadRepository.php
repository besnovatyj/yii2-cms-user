<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\repositories;

use Besnovatyj\User\components\UserStatus;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\repositories\NotFoundException;

class UserReadRepository
{
    public function find(int $id): ?User
    {
        return User::findOne($id);
    }

    public function findActiveByUsername(string $username): ?User
    {
        return User::findOne(['username' => $username, 'status' => UserStatus::STATUS_ACTIVE]);
    }

    public function findActiveById(int $id): ?User
    {
        return User::findOne(['id' => $id, 'status' => UserStatus::STATUS_ACTIVE]);
    }

    /**
     * @throws  NotFoundException
     */
    public function getActiveById(int $id): User
    {
        /** @var $user \Besnovatyj\User\entities\User */
        $user = User::find()->andWhere(['id' => $id, 'status' => UserStatus::STATUS_ACTIVE])->with(['profile'])->limit(1)->one();
        if (!$user) {
            throw new NotFoundException('Пользователь не найден.');
        }
        return $user;
    }
}
