<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\frontend;

use Besnovatyj\User\validators\PasswordValidator;
use yii\base\Model;

class ResetPasswordForm extends Model
{
    public $password;
    public $reCaptcha;

    public function rules(): array
    {
        return [
            ['password', 'required'],
            ['password', PasswordValidator::class],
            [['reCaptcha'], ReCaptchaValidator2::class,
                'message' => 'Invalid captcha value'
            ],
        ];
    }
}
