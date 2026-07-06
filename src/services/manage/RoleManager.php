<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\manage;

use DomainException;
use Exception;
use yii\rbac\ManagerInterface;

class RoleManager
{
    private ManagerInterface $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @throws Exception
     */
    public function assign($userId, $name): void
    {
        $am = $this->manager;
        if (!$role = $am->getRole($name)) {
            throw new DomainException('Role "' . $name . '" does not exist.');
        }
        $am->revokeAll($userId);
        $am->assign($role, $userId);
    }

    public function revokeAll($userId): void
    {
        $am = $this->manager;
        $am->revokeAll($userId);
    }
}
