<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\backend;

use Besnovatyj\User\components\UserStatus;
use Besnovatyj\User\entities\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class UserSearch extends Model
{
    public string|null $id = null;
    public string|null $date_from = null;
    public string|null $date_to = null;
    public string|null $username = null;
    public string|null $email = null;
    public string|null $status = null;
    public string|null $role = null;

    public function rules(): array
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'email', 'role'], 'string'],
            // [['date_from', 'date_to'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            [['date_from', 'date_to'], 'datetime', 'format' => 'php:Y-m-d H:i'],
            ['status', 'in', 'range' => [UserStatus::STATUS_ACTIVE, UserStatus::STATUS_BLOCKED, UserStatus::STATUS_WAIT]],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = User::find()->alias('u');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'u.id' => $this->id,
            'u.status' => $this->status,
        ]);

        if (!empty($this->role)) {
            $query->innerJoin('{{%auth_assignments}} a', 'a.user_id = u.id');
            $query->andWhere(['a.item_name' => $this->role]);
        }

        $query
            ->andFilterWhere(['like', 'u.username', $this->username])
            ->andFilterWhere(['like', 'u.email', $this->email])
            ->andFilterWhere(['>=', 'u.created_at', $this->date_from ? $this->date_from . ':00' : null])
            ->andFilterWhere(['<=', 'u.created_at', $this->date_to ? $this->date_to . ':00' : null]);
        return $dataProvider;
    }

    public function rolesList(): array
    {
        return ArrayHelper::map(\Yii::$app->authManager->getRoles(), 'name', 'description');
    }
}
