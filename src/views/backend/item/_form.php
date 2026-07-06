<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\components\ItemController;
use Besnovatyj\User\components\RouteRule;
use Besnovatyj\User\entities\AuthItem;
use Besnovatyj\Helpers\json\Json;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\web\View;

/* @var $this View */
/* @var $model AuthItem */
/* @var $form ActiveForm */
/* @var $context ItemController */

$rules = Yii::$app->getAuthManager()->getRules();
unset($rules[RouteRule::RULE_NAME]);
$source = Json::encode(array_keys($rules));

$this->registerJs(file_get_contents(__DIR__ . '/autocomplete.js'), $this::POS_END);
?>

<div id="auth-item-form" data-autocomplete-source='<?= $source ?>'>
    <?php $form = ActiveForm::begin(['id' => 'item-form']); ?>
    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['class' => 'form-control', 'maxlength' => 64]) ?>

            <?= $form->field($model, 'description')->textarea(['class' => 'form-control', 'rows' => 6]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'ruleName')->textInput(['class' => 'form-control', 'id' => 'rule_name']) ?>

            <?= $form->field($model, 'data')->textarea(['class' => 'form-control', 'rows' => 6]) ?>
        </div>
    </div>
    <div class="form-group">
        <div class="d-grid">
            <?php
            echo Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'name' => 'submit-button'])
            ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
