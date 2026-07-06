<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\backend;

use Besnovatyj\Forms\BaseForm;
use Besnovatyj\User\components\UserSex;
use Besnovatyj\User\entities\Profile;
use yii\web\UploadedFile;

/**
 * Форма редактирования профиля пользователя в админке.
 * Загрузка фото аналогична сущности поста блога (UploadBehavior).
 */
class ProfileForm extends BaseForm
{
    public $photo;
    public $firstName;
    public $lastName;
    public $sex;

    public function __construct(?Profile $profile = null, $config = [])
    {
        if ($profile) {
            $this->photo = $profile->photo;
            $this->firstName = $profile->firstName;
            $this->lastName = $profile->lastName;
            $this->sex = $profile->sex;
        }
        parent::__construct($config);
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->photo = UploadedFile::getInstance($this, 'photo');
            return true;
        }
        return false;
    }

    public function rules(): array
    {
        return [
            [['firstName', 'lastName'], 'string', 'max' => 255],
            [['photo'], 'image',
                'skipOnEmpty' => true,
                'extensions' => 'png, jpg, jpeg, webp',
                'minWidth' => '100',
                'maxWidth' => '5000',
                'minHeight' => '100',
                'maxHeight' => '5000',
            ],
            ['sex', 'in', 'range' => [UserSex::SEX_MALE, UserSex::SEX_FEMALE, null, '']],
        ];
    }

    public function sexList(): array
    {
        return [
            UserSex::SEX_MALE => 'Мужской',
            UserSex::SEX_FEMALE => 'Женский',
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'photo' => 'Фото профиля',
            'firstName' => 'Имя',
            'lastName' => 'Фамилия',
            'sex' => 'Пол',
        ];
    }
}
