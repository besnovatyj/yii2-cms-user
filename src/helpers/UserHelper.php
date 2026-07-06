<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\helpers;

use Exception;
use Besnovatyj\User\components\Rbac;
use Besnovatyj\User\components\UserStatus;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class UserHelper
{
    /**
     * @throws Exception
     */
    public static function statusName($status): string
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }

    public static function statusList(): array
    {
        return [
            UserStatus::STATUS_WAIT => 'Ожидает',
            UserStatus::STATUS_ACTIVE => 'Активный',
            UserStatus::STATUS_BLOCKED => 'Заблокирован',
        ];
    }

    /**
     * @throws Exception
     */
    public static function statusLabel($model): string // TODO уже 8 раза повторяется... в трейт,?
    {
        switch ($model->status) {
            case UserStatus::STATUS_WAIT:
                $class = 'badge bg-secondary';
                $action = 'activate';
                $confirmMessage = 'Активировать пользователя?';
                break;
            case UserStatus::STATUS_ACTIVE:
                $class = 'badge bg-success';
                $action = 'block';
                $confirmMessage = 'Блокировать пользователя?';
                break;
            case UserStatus::STATUS_BLOCKED:
                $class = 'badge bg-danger';
                $action = 'activate';
                $confirmMessage = 'Активировать пользователя?';
                break;
            default:
                $class = 'badge bg-secondary';
                $action = 'activate';
                $confirmMessage = 'Активировать пользователя?';
        }

        $text = Html::tag('span', ArrayHelper::getValue(self::statusList(), $model->status), [
            'class' => $class,
        ]);
        $url = Url::to([$action, 'id' => $model->id]);
        return Html::a($text, $url, [
            'data' => [
                'confirm' => $confirmMessage,
                'method' => 'post',
            ],
        ]);

    }
}
