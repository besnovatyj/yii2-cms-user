<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\backend;

use Besnovatyj\User\components\ItemController;
use yii\rbac\Item;

/**
 * 00000000000000000000000
 */
class RoleController extends ItemController
{

    public function labels(): array
    {
        return [
            'Item' => 'Role',
            'Items' => 'Roles',
        ];
    }

    public function getType(): int
    {
        return Item::TYPE_ROLE;
    }
}
