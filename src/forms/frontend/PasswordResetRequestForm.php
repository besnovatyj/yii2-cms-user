<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\frontend;

use Besnovatyj\User\components\UserStatus;
use Besnovatyj\User\entities\User;
use yii\base\Model;

class PasswordResetRequestForm extends Model
{
    public $email;
    public $reCaptcha;

    public function rules(): array
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                'filter' => ['status' => UserStatus::STATUS_ACTIVE],
                'message' => 'Нет пользователя с таким e-mail.'
            ],
            [['reCaptcha'], ReCaptchaValidator2::class,
                'message' => 'Invalid captcha value'
            ],
        ];
    }
}
