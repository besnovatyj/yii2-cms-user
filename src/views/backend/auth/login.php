<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\forms\LoginForm;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $model LoginForm */

$this->title = 'Sign In';

$this->context->layout = '@app/views/layouts/blank';

?>
<main class="form-signin w-100 m-auto">
    <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>
    <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
    <?= $form
        ->field($model, 'username')
        ->textInput(['placeholder' => $model->getAttributeLabel('username'), 'class' => 'form-control'])
        ->label(false)
    ?>
    <?= $form
        ->field($model, 'password')
        ->passwordInput(['placeholder' => $model->getAttributeLabel('password'), 'class' => 'form-control'])
        ->label(false)
    ?>
    <?= $form->field($model, 'rememberMe')->checkbox() ?>
    <div class="d-flex justify-content-center mb-2">
        <?= \Besnovatyj\Altcha\widgets\AltchaWidget::widget([
            'name' => 'altcha',
            'challengeUrl' => \yii\helpers\Url::to(['/Altcha/backend/altcha-challenge/challenge']),
            'options' => [ // сюда можно класть любые атрибуты <altcha-widget>
//                'hidefooter' => true,
            ],
        ]); ?>
    </div>
    <div class="form-group">
        <div class="d-grid">
            <?= Html::submitButton('Sign in', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</main>
