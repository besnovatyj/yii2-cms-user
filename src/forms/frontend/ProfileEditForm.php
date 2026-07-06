<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\frontend;

use Besnovatyj\User\components\UserSex;
use Besnovatyj\User\entities\Profile;
use yii\base\Model;
use yii\web\UploadedFile;

class ProfileEditForm extends Model
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

    public function rules(): array
    {
        return [
            [['firstName', 'lastName'], 'required'],
            [['firstName', 'lastName'], 'string', 'max' => 255],
            ['photo', 'image'],
            ['sex', 'in', 'range' => [UserSex::SEX_MALE, UserSex::SEX_FEMALE, '']],
        ];
    }

    public function beforeValidate(): bool
    {
        if (parent::beforeValidate()) {
            $this->photo = UploadedFile::getInstance($this, 'photo');
            return true;
        }
        return false;
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
