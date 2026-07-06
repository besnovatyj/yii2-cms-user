<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\backend;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Helpers\phone\PhoneValidator;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\validators\UsernameValidator;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property ProfileForm $profile
 */
class UserEditForm extends CompositeForm
{
    public string $username = '';
    public string $email = '';
    public string $phone = '';
    public string $role = '';
    public string $description = '';

    public User $_user;

    public function __construct(User $user, $config = [])
    {
        $this->username = $user->username;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->description = $user->description;
        // TODO - Что за логика по назначению только одной из ролей при редактировании?
        //  Убрать отсюда редактирование ролей?
        $roles = Yii::$app->authManager->getRolesByUser($user->id);
        $this->role = $roles ? reset($roles)->name : null;
        $this->_user = $user;
        $this->profile = new ProfileForm($user->profile);
        parent::__construct($config);
    }

    public function beforeValidate(): bool
    {
        $this->username = trim(preg_replace('/\s+/u', '', $this->username));
        $this->email = trim(preg_replace('/\s+/u', '', $this->email));
        $this->phone = trim(preg_replace('/\s+/u', '', $this->phone));
        return parent::beforeValidate();
    }

    public function rules(): array
    {
        return [
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->_user->id], 'message' => 'Данный логин уже занят.'],
            ['username', UsernameValidator::class, 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->_user->id]],
            ['username', 'string', 'min' => 3, 'max' => 255],

            ['email', 'required'],
            ['email', 'string', 'min' => 5, 'max' => 255],
            ['email', 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->_user->id], 'message' => 'Данный адрес электронной почты уже используется.'],
            ['email', 'email'],

//            ['phone', 'required'],
            ['phone', 'string', 'max' => 255],
            ['phone', 'unique', 'targetClass' => User::class, 'filter' => ['<>', 'id', $this->_user->id], 'message' => 'Данный номер телефона уже используется.'],
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
            'role' => 'Роль',
        ];
    }

    protected function internalForms(): array
    {
        return ['profile'];
    }
}
