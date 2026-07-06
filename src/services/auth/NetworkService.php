<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\auth;

use DomainException;
use Besnovatyj\User\entities\Network;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\repositories\UserRepository;
use Throwable;
use Yii;
use yii\db\Exception;

class NetworkService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function auth($network, $identity): User
    {
        if ($user = $this->users->findByNetworkIdentity($network, $identity)) {
            return $user;
        }

        $user = User::signupByNetwork();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->users->save($user);

            $this->attachNetwork($user, $network, $identity);

            $transaction->commit();
            return $user;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function attach($id, $network, $identity): void
    {
        if ($this->users->findByNetworkIdentity($network, $identity)) {
            throw new DomainException('Соц-сеть уже привязана.');
        }

        $user = $this->users->get($id);

        // Check if this network is already attached to this user
        foreach ($user->networks as $current) {
            if ($current->isFor($network, $identity)) {
                throw new DomainException('Network is already attached.');
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->attachNetwork($user, $network, $identity);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function detach($network_id, $user_id): void
    {
        if (!$this->users->findByIdUserId($network_id, $user_id)) {
            throw new DomainException('Данная соц-сеть не привязана.');
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            Network::deleteAll(['id' => $network_id, 'user_id' => $user_id]);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    // ==================== Private methods ====================

    /**
     * @throws Exception
     */
    private function attachNetwork(User $user, $network, $identity): void
    {
        $networkEntity = Network::create($network, $identity);
        $networkEntity->user_id = $user->id;

        if (!$networkEntity->save()) {
            throw new Exception('Failed to save network.');
        }
    }
}
