<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\search;

use Besnovatyj\User\components\RouteRule;
use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;

/**
 * Description of RuleSearch
 */
class RuleSearch extends Model
{
    /** @var string name of the rule */
    public $name;
    public $className;

    public function rules(): array
    {
        return [
            [['name', 'className'], 'string']
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('rbac-admin', 'Name'),
            'className' => Yii::t('rbac-admin', 'Class Name'),
        ];
    }

    /**
     * Search RuleSearch
     * @param array $params
     * @return ArrayDataProvider
     */
    public function search(array $params): ArrayDataProvider
    {
        /* @var yii\rbac\DbManager $authManager */
        $authManager = \Yii::$app->getAuthManager();
        $models = [];

        $this->load($params);

        if ($this->validate()) {
            foreach ($authManager->getRules() as $name => $item) {
                if (
                    $name != RouteRule::RULE_NAME &&
                    (trim((string)$this->name) == '' || stripos($item->name, $this->name) !== false) &&
                    (trim((string)$this->className) == '' || stripos(get_class($item), $this->className) !== false)
                ) {
                    $models[$name] = ['name' => $name, 'className' => get_class($item)];
                }
            }
        }
        return new ArrayDataProvider([
            'allModels' => $models,
        ]);
    }
}
