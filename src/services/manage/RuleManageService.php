<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\services\manage;

use DomainException;
use Exception;
use Besnovatyj\User\forms\backend\RuleForm;
use yii\rbac\ManagerInterface;
use yii\rbac\Rule;
use yii\web\NotFoundHttpException;

class RuleManageService
{
    protected ManagerInterface $authManager;

    public function __construct(
        ManagerInterface $authManager,
    )
    {
        $this->authManager = $authManager;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function getModel(string $id): Rule
    {
        if ($item = $this->authManager->getRule($id)) {
            return $item;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @throws Exception
     */
    public function create(RuleForm $form): Rule
    {
        /** @var $rule Rule */
        $rule = new $form->className();
        $rule->name = $form->name;
        $this->authManager->add($rule);

        return $rule;
    }

    /**
     * @throws Exception
     */
    public function update(RuleForm $form): Rule
    {
        /** @var $rule Rule */
        $rule = new $form->className();
        $rule->name = $form->name;

        $this->authManager->update($form->getItem()->name, $rule);

        return $rule;

    }

    public function remove(Rule $rule): void
    {
        if (!$this->authManager->remove($rule)) {
            throw new DomainException('Failed to delete rule: ' . $rule->name);
        }
    }

}
