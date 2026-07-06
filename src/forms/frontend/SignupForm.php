<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\frontend;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Helpers\phone\PhoneValidator;
use Besnovatyj\User\entities\Profile;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\validators\PasswordValidator;
use Besnovatyj\User\validators\UsernameValidator;

/**
 * @property Profile $profile
 */
class SignupForm extends CompositeForm
{
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';

    public function __construct($config = [])
    {
        $this->profile = new ProfileEditForm();
        parent::__construct($config);
    }

    public function beforeValidate(): bool
    {
        $this->username = trim(preg_replace('/\s+/u', '', $this->username));
        $this->email = trim(preg_replace('/\s+/u', '', $this->email));
        $this->phone = trim(preg_replace('/\s+/u', '', $this->phone));
        $this->password = trim(preg_replace('/\s+/u', '', $this->password));
        return parent::beforeValidate();
    }

    public function rules(): array
    {
        return [
            ['username', 'trim'],
//            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'Данный логин уже занят.'],
            ['username', UsernameValidator::class],
            ['username', 'string', 'min' => 3, 'max' => 255],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Данный адрес электронной почты уже используется.'],
            ['email', 'email'],

            ['password', 'required'],
            ['password', PasswordValidator::class],

//            ['phone', 'required'],
            ['phone', 'string', 'max' => 255],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => 'Данный номер телефона уже используется.'],
            ['phone', PhoneValidator::class],

        ];
    }

    protected function internalForms(): array
    {
        return ['profile'];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'email' => 'Электронная почта',
            'password' => 'Пароль',
            'phone' => 'Номер телефона',
        ];
    }

}
