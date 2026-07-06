<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\forms\backend\UserCreateForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model UserCreateForm */

$this->title = 'Create user';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(); ?>
    <div class="card">
        <div class="card-header"><?= $this->title ?></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'role')->dropDownList($model->rolesList(), ['prompt' => 'Не выбрано', 'class' => 'custom-select']) ?>
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                    <?= $form->field($model, 'password')->passwordInput(['maxLength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                    <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                    <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
            <div class="form-group">
                <div class="d-grid">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /.card -->
<?php ActiveForm::end(); ?>
