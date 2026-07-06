<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\rest;

use Exception;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\helpers\UserHelper;
use yii\rest\Controller;

class ProfileController extends Controller
{
    public function verbs(): array
    {
        return [
            'index' => ['get'],
        ];
    }

    /**
     * @throws Exception
     */
    public function actionIndex(): array
    {
        return $this->serializeUser($this->findModel());
    }

    private function findModel(): User
    {
        return User::findOne(\Yii::$app->user->id);
    }

    /**
     * @throws Exception
     */
    private function serializeUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->username,
            'email' => $user->email,
            'date' => [
                'created' => $user->created_at,
                'updated' => $user->updated_at,
            ],
            'status' => [
                'code' => $user->status,
                'name' => UserHelper::statusName($user->status),
            ],
        ];
    }
}
