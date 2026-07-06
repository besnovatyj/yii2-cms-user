<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\widgets\grid;

use Besnovatyj\User\components\Rbac;
use Yii;
use yii\grid\DataColumn;
use yii\helpers\Html;
use yii\rbac\Item;

class RoleColumn extends DataColumn
{
    protected function renderDataCellContent($model, $key, $index): string
    {
        $roles = Yii::$app->authManager->getRolesByUser($model->id);
        return $roles === [] ? $this->grid->emptyCell : implode(', ', array_map(function (Item $role) {
            return $this->getRoleLabel($role);
        }, $roles));
    }

    private function getRoleLabel(Item $role): string
    {
        $class = match ($role->name) {
            Rbac::ROLE_GUEST => 'secondary',
            Rbac::ROLE_USER => 'primary',
            Rbac::ROLE_ROOT => 'danger',
        };
        return Html::tag('span', Html::encode($role->description), ['class' => 'badge bg-' . $class]);
    }
}
