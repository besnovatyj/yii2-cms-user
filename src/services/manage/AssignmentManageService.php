<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\manage;

use Exception;
use InvalidArgumentException;
use Besnovatyj\User\components\Helper;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\repositories\UserRepository;
use yii\rbac\Item;
use yii\rbac\ManagerInterface;

class AssignmentManageService
{
    protected $user = null;
    protected $usersRepo;
    protected $authManager;

    public function __construct(
        UserRepository   $usersRepo,
        ManagerInterface $authManager,
    )
    {
        $this->usersRepo = $usersRepo;
        $this->authManager = $authManager;
    }

    /**
     * @throws Exception
     */
    public function assign(int $id, array $items): array
    {
        $user = $this->getUser($id); // Проверяем существование пользователя
        foreach ($items as $name) {
            $item = $this->getItem($name);
            if ($this->isAlreadyAssigned($item, $user->id)) {
                throw new InvalidArgumentException($item->name . ' item is already assigned.');
            }
            $this->authManager->assign($item, $user->id);
        }
        Helper::invalidate();

        return $this->getItems($user->id);
    }

    public function revoke(int $id, array $items): array
    {
        $user = $this->getUser($id); // Проверяем существование пользователя
        foreach ($items as $name) {
            $item = $this->getItem($name);
            if (!$this->isAlreadyAssigned($item, $user->id)) {
                throw new InvalidArgumentException($item->name . ' item is not assigned with this user.');
            }
            $this->authManager->revoke($item, $user->id);
        }
        Helper::invalidate();
        return $this->getItems($user->id);
    }

    public function getItems(int $id): array
    {
        $user = $this->getUser($id); // Проверяем существование пользователя
        $available = [];
        foreach (array_keys($this->authManager->getRoles()) as $name) {
            $available[$name] = 'role';
        }
        foreach (array_keys($this->authManager->getPermissions()) as $name) {
            if ($name[0] != '/') {
                $available[$name] = 'permission';
            }
        }

        $assigned = [];
        foreach ($this->authManager->getAssignments($user->id) as $item) {
            $assigned[$item->roleName] = $available[$item->roleName];
            unset($available[$item->roleName]);
        }

        ksort($available);
        ksort($assigned);
        return [
            'available' => $available,
            'assigned' => $assigned,
        ];
    }

    /**
     * @return Item[]
     */
    protected function findExistedItems(): array
    {
        $allRoles = $this->authManager->getRoles();
        $allPermissions = array_filter($this->authManager->getPermissions(), function ($item) {
            return $item->name[0] != '/';
        });
        return array_merge($allRoles, $allPermissions);
    }

    protected function getItem(string $name): Item
    {
        $existedItems = $this->findExistedItems();
        if (!in_array($name, array_keys($existedItems))) {
            throw new InvalidArgumentException($name . ' item is not exist.');
        }
        return $existedItems[$name];
    }

    protected function isAlreadyAssigned(Item $item, int $id): bool
    {
        $userAsgmts = array_keys($this->authManager->getAssignments($id));
        return in_array($item->name, $userAsgmts);
    }

    public function getUser(int $id): User
    {
        if (!$this->user) {
            $this->user = $this->usersRepo->get($id);
        }
        return $this->user;
    }

}
