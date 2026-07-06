<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms;

use Besnovatyj\Altcha\validators\AltchaValidator;
use yii\base\Model;

class LoginForm extends Model
{
    public string $username = '';
    public string $password = '';
    public bool $rememberMe = true;
    public string $altcha = '';

    public function rules(): array
    {
        return [
            [['username', 'password'], 'required'],
            ['rememberMe', 'boolean'],
            ['altcha', AltchaValidator::class],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин или e-mail',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить меня',
        ];
    }
}
