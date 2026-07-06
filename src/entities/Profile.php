<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\entities;

use Besnovatyj\Upload\heap\ThumbnailMode;
use Besnovatyj\Upload\heap\ThumbnailProfile;
use Besnovatyj\Upload\heap\UploadBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property int $user_id
 * @property string $photo
 * @property string $firstName
 * @property string $lastName
 * @property int $sex
 *
 * @mixin UploadBehavior
 */
class Profile extends ActiveRecord
{
    public static function create(int $userId, $sex, $firstName, $lastName): self
    {
        $profile = new static();
        $profile->user_id = $userId;
        $profile->sex = $sex;
        $profile->firstName = $firstName;
        $profile->lastName = $lastName;
        return $profile;
    }

    public function edit($sex, $firstName, $lastName): void
    {
        $this->sex = $sex;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    public function setPhoto(UploadedFile $photo): void
    {
        $this->photo = $photo;
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

    public static function tableName(): string
    {
        return '{{%users_profiles}}';
    }

    public function behaviors(): array
    {
        return [
            'photoUpload' => [
                'class' => UploadBehavior::class,
                'attribute' => 'photo',
                'pathTemplate' => 'origin/User/{attr.user_id}/{basename}',
                'thumbnails' => [
                    new ThumbnailProfile('admin', width: 100, height: 70, quality: 80, mode: ThumbnailMode::Crop),
                    new ThumbnailProfile('thumb', width: 370, height: 370, mode: ThumbnailMode::Resize),
                ],
                'thumbPathTemplate' => 'cache/User/{attr.user_id}/{filename}_{profile}.{extension}',
            ],
            ...parent::behaviors(),
        ];
    }

}
