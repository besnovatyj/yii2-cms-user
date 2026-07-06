<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\helpers;

use Exception;
use Besnovatyj\User\components\UserSex;
use yii\helpers\ArrayHelper;

class UserProfileHelper
{
    /**
     * @throws Exception
     */
    public static function sexName($sex): ?string
    {
        return ArrayHelper::getValue(self::sexList(), $sex);
    }

    public static function sexList(): array
    {
        return [
            UserSex::SEX_MALE => 'Мужской',
            UserSex::SEX_FEMALE => 'Женский',
        ];
    }

}
