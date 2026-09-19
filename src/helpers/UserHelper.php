<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\helpers;

use Besnovatyj\User\components\Rbac;
use Besnovatyj\User\components\UserStatus;
use Besnovatyj\User\entities\User;
use yii\helpers\Html;
use yii\helpers\Url;

class UserHelper
{
    /**
     * Название статуса. Принимает как сам статус, так и его числовое значение
     * (например, из GET-параметра фильтра).
     */
    public static function statusName(UserStatus|int|string|null $status): string
    {
        if (!$status instanceof UserStatus) {
            if ($status === null || $status === '') {
                return '';
            }
            $status = UserStatus::tryFrom((int)$status);
        }
        return $status?->label() ?? '';
    }

    /**
     * Список статусов для фильтров и выпадающих списков.
     * @return array<int, string>
     */
    public static function statusList(): array
    {
        return UserStatus::labels();
    }

    public static function statusLabel(User $model): string // TODO уже 8 раза повторяется... в трейт,?
    {
        [$class, $action, $confirmMessage] = match ($model->status) {
            UserStatus::STATUS_ACTIVE => ['badge bg-success', 'block', 'Блокировать пользователя?'],
            UserStatus::STATUS_BLOCKED => ['badge bg-danger', 'activate', 'Активировать пользователя?'],
            default => ['badge bg-secondary', 'activate', 'Активировать пользователя?'],
        };

        $text = Html::tag('span', self::statusName($model->status), [
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
