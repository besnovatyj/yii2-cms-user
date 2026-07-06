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
class PermissionController extends ItemController
{

    public function labels(): array
    {
        return [
            'Item' => 'Permission',
            'Items' => 'Permissions',
        ];
    }

    public function getType(): int
    {
        return Item::TYPE_PERMISSION;
    }
}
