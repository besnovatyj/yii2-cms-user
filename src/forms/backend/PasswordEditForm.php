<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\backend;

use Besnovatyj\User\validators\PasswordValidator;
use yii\base\Model;

class PasswordEditForm extends Model
{
    public string $password = '';

    public function __construct(array $config = [])
    {
        $this->password = '';
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['password', 'required'],
            YII_DEBUG ? ['password', 'string', 'min' => 3] : ['password', PasswordValidator::class],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'password' => 'Password',
        ];
    }
}
