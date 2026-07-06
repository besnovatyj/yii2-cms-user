<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\forms\backend\RuleForm;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\web\View;

/* @var $this  View */
/* @var $model RuleForm */
/* @var $form ActiveForm */
?>

<div id="auth-item-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['class' => 'form-control', 'maxlength' => 64]) ?>

    <?= $form->field($model, 'className')->textInput(['class' => 'form-control', ]) ?>

    <div class="form-group">
        <div class="d-grid">
        <?php
        echo Html::submitButton($model->isNewRecord ? Yii::t('rbac-admin', 'Create') : Yii::t('rbac-admin', 'Update'), [
            'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
        ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
