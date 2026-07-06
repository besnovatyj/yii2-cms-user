<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\repositories;

use Besnovatyj\DomainEvents\dispatchers\EventDispatcher;
use Besnovatyj\User\components\UserStatus;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\repositories\NotFoundException;
use RuntimeException;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class UserRepository
{
    private EventDispatcher $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function findActiveByUsernameOrEmail(string $value): ?User
    {
        return $this->findBy(['or', ['username' => $value], ['email' => $value]]);
    }

    public function findActiveByPasswordResetToken(string $token): ?User
    {
        return $this->findBy(['password_reset_token' => $token, 'status' => UserStatus::STATUS_ACTIVE]);
    }

    public function findByNetworkIdentity(string $network, string $identity): ?User
    {
        /** @var $user User */
        $user = User::find()->joinWith('networks n')->andWhere(['n.network' => $network, 'n.identity' => $identity])->one();
        return $user;
    }

    public function findByIdUserId(string $network_id, int $user_id): ?User
    {
        /** @var $user User */
        $user = User::find()->joinWith('networks n')->andWhere(['n.id' => $network_id, 'n.user_id' => $user_id])->one();
        return $user;
    }

    public function find(int $id): ?User
    {
        return User::findOne($id);
    }

    public function get(int $id): User
    {
        return $this->getBy(['id' => $id]);
    }

    public function getByEmailConfirmToken(string $token): User
    {
        return $this->getBy(['email_confirm_token' => $token]);
    }

    public function getByPhoneConfirmToken(string $token): User
    {
        return $this->getBy(['phone_confirm_token' => $token]);
    }

    public function getByEmail(string $email): User
    {
        return $this->getBy(['email' => $email]);
    }

    public function getActiveByPasswordResetToken(string $token): User
    {
        return $this->getBy([
            'password_reset_token' => $token,
            'status' => UserStatus::STATUS_ACTIVE,
        ]);
    }

    private function getBy(array $condition): User
    {
        /** @var $user User */
        if (!$user = User::find()->andWhere($condition)->limit(1)->one()) {
            throw new NotFoundException('Пользователь не найден.');
        }
        return $user;
    }

    private function findBy(array $condition): ?User
    {
        /** @var $user User */
        $user = User::find()->andWhere($condition)->andWhere(['status' => UserStatus::STATUS_ACTIVE])->limit(1)->one();
        return $user;
    }

    /**
     * @throws Exception
     */
    public function save(User $user): void
    {
        if (!$user->save()) {
            throw new RuntimeException('Ошибка сохранения.');
        }
        $this->dispatcher->dispatchAll($user->releaseEvents());
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function remove(User $user): void
    {
        if (!$user->delete()) {
            throw new RuntimeException('Ошибка удаления.');
        }
        $this->dispatcher->dispatchAll($user->releaseEvents());
    }

    public function findAnyByEmail(string $email): ?User
    {
        return User::find()->andWhere(['email' => $email])->limit(1)->one();
    }
}
