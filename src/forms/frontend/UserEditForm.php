<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\frontend;

use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\User\entities\Profile;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\frontend\ProfileEditForm;

/**
 * @property Profile $profile
 */
class UserEditForm extends CompositeForm
{
    public $username;
    public $phone;
    public $email;

    protected $_user;

    public function __construct(User $user, $config = [])
    {
        $this->username = $user->username;
        $this->phone = $user->phone;
        $this->email = $user->email;
        $this->_user = $user;
        $this->profile = new ProfileEditForm($user->profile);
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['email',], 'required'],
            ['email', 'email'],
            [['email', 'username'], 'string', 'max' => 255],
//            ['username', UsernameValidator::class],
//          ['phone', PhoneValidator::class],
            [['phone', 'email', 'username'], 'unique', 'targetClass' => User::class, 'filter' => ['!=', 'id', $this->_user->id]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'email' => 'E-mail',
            'phone' => 'Номер телефона',
        ];
    }

    protected function internalForms(): array
    {
        return ['profile'];
    }
}
