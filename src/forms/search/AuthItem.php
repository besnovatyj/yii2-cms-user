<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\search;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\rbac\Item;
use yii\rbac\ManagerInterface;

/**
 * AuthItemSearch represents the model behind the search form about AuthItem.
 */
class AuthItem extends Model
{
    const int TYPE_ROUTE = 101;

    public $name;
    public $type;
    public $description;
    public $ruleName;
    public $data;


    public function rules(): array
    {
        return [
            [['name', 'ruleName', 'description'], 'safe'],
            [['type'], 'integer'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('rbac-admin', 'Name'),
            'item_name' => Yii::t('rbac-admin', 'Name'),
            'type' => Yii::t('rbac-admin', 'Type'),
            'description' => Yii::t('rbac-admin', 'Description'),
            'ruleName' => Yii::t('rbac-admin', 'Rule Name'),
            'data' => Yii::t('rbac-admin', 'Data'),
        ];
    }

    /**
     * Search authItem
     * @param array $params
     * @return
     * @throws
     */
    public function search($params)
    {
        /* @var ManagerInterface $authManager */
        $authManager = \Yii::$app->getAuthManager();
        if ($this->type == Item::TYPE_ROLE) {
            $items = $authManager->getRoles();
        } else {
            $items = array_filter($authManager->getPermissions(), function ($item) {
                $isPermission = $this->type == Item::TYPE_PERMISSION;
                return $isPermission xor strncmp($item->name, '/', 1) === 0;
            });
        }
        $this->load($params);
        if ($this->validate()) {

            $search = mb_strtolower(trim((string)$this->name));
            $desc = mb_strtolower(trim((string)$this->description));
            $ruleName = $this->ruleName;
            foreach ($items as $name => $item) {
                $f = (empty($search) || mb_strpos(mb_strtolower($item->name), $search) !== false) &&
                    (empty($desc) || mb_strpos(mb_strtolower($item->description), $desc) !== false) &&
                    (empty($ruleName) || $item->ruleName == $ruleName);
                if (!$f) {
                    unset($items[$name]);
                }
            }
        }

        return new ArrayDataProvider([
            'allModels' => $items,
        ]);
    }
}
