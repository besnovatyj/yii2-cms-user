<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\forms\backend;

use Yii;
use yii\base\Model;
use yii\rbac\Rule;

class RuleForm extends Model
{
    public string $name = '';
    /** @var string Rule classname. */
    public string $className = '';
    /** @var ?Rule */
    private ?Rule $_item = null;

    public function __construct(?Rule $rule = null, $config = [])
    {
        if ($rule) {
            $this->name = $rule->name;
            $this->className = get_class($rule);
            $this->_item = $rule;
        }
        parent::__construct($config);
    }

    /**
     * Check if new record.
     * @return boolean
     */
    public function getIsNewRecord(): bool
    {
        return $this->_item === null;
    }

    public function rules(): array
    {
        return [
            [['name', 'className'], 'required'],
            [['name',], 'string', 'max' => 64],
            [['className',], 'string'],
            [['className',], 'classExists']
        ];
    }

    /**
     * Validate class exists
     */
    public function classExists(): void
    {
        if (!class_exists($this->className)) {
            $message = Yii::t('rbac-admin', "Unknown class '{class}'", ['class' => $this->className]);
            $this->addError('className', $message);
            return;
        }
        if (!is_subclass_of($this->className, Rule::class)) {
            $message = Yii::t('rbac-admin', "'{class}' must extend from 'yii\rbac\Rule' or its child class", [
                'class' => $this->className]);
            $this->addError('className', $message);
        }
    }

    /**
     * Get item
     * @return Rule
     */
    public function getItem(): Rule
    {
        return $this->_item;
    }

    public function attributeLabels(): array
    {
        return [
            'name' => Yii::t('rbac-admin', 'Name'),
            'className' => Yii::t('rbac-admin', 'Class Name'),
        ];
    }


}
