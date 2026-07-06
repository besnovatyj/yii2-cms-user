<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\backend;

use Besnovatyj\Helpers\phone\PhoneValidator;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\validators\PasswordValidator;
use Besnovatyj\User\validators\UsernameValidator;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class UserCreateForm extends Model
{
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $description = '';
    public string $role = '';

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
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'Данный логин уже занят.'],
            ['username', UsernameValidator::class],
            ['username', 'string', 'min' => 3, 'max' => 255],

            ['email', 'required'],
            ['email', 'string', 'min' => 5, 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'message' => 'Данный адрес электронной почты уже используется.'],
            ['email', 'email'],

            ['password', 'required'],
            YII_DEBUG ? ['password', 'string', 'min' => 3] : ['password', PasswordValidator::class],

//            ['phone', 'required'],
            ['phone', 'string', 'max' => 255],
            ['phone', 'unique', 'targetClass' => User::class, 'message' => 'Данный номер телефона уже используется.'],
            // С фильтром только на update??
            // [['phone'], 'unique', 'targetClass' => User::class, 'filter' => $this->phone ? ['<>', 'phone', $this->phone] : null],
            ['phone', PhoneValidator::class],

            ['role', 'required'],

            ['description', 'string'],
        ];
    }

    public function rolesList(): array
    {
        return ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'description');
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'email' => 'Электронный адрес',
            'phone' => 'Номер телефона',
            'description' => 'Описание',
            'password' => 'Пароль',
            'role' => 'Роль',
        ];
    }

}
