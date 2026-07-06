<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\entities;

use common\components\upload\behaviors\ImageUploadBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property int $user_id
 * @property string $photo
 * @property int $firstName
 * @property int $lastName
 * @property int $sex
 *
 * @mixin ImageUploadBehavior
 */
class Profile extends ActiveRecord // todo - удалять профайл при удалении юзера
{
    public static function create(int $userId, $sex, $firstName, $lastName): self
    {
        $profile = new static();
        $profile->user_id = $userId;
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

    public static function tableName(): string
    {
        return '{{%users_profiles}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'photo',
                'filePath' => '@static/origin/users/[[attribute_user_id]]/[[id]].[[extension]]',
                'fileUrl' => '@staticHostName/origin/users/[[attribute_user_id]]/[[id]].[[extension]]',
                'thumbPath' => '@static/cache/users/[[attribute_user_id]]/[[profile]]_[[id]].[[extension]]',
                'thumbUrl' => '@staticHostName/cache/users/[[attribute_user_id]]/[[profile]]_[[id]].[[extension]]',
                'thumbs' => [
                    'admin' => ['width' => 100, 'height' => 70],
                    'thumb' => ['width' => 370, 'height' => 370],
                ],
            ],
            ...parent::behaviors(),
        ];
    }

}
