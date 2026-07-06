<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\backend\UserEditForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model UserEditForm */
/* @var $user User */

if (!empty($user->username)) {
    $userTitle = $user->username;
} elseif (!empty($user->email)) {
    $userTitle = $user->email;
} else {
    $userTitle = $user->id;
}

$this->title = 'Update user: ' . $userTitle;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $userTitle, 'url' => ['view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<div class="card">
    <div class="card-header">Profile user - <?= $userTitle ?></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'role')->dropDownList($model->rolesList(), ['prompt' => 'Не выбрано', 'class' => 'custom-select']) ?>
                <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                <?= $form->field($model, 'email')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'phone')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <div class="form-group">
            <div class="d-grid">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
</div>
<!-- /.card -->

<div class="card">
    <div class="card-header">Данные профиля</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model->profile, 'firstName')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                <?= $form->field($model->profile, 'lastName')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                <?= $form->field($model->profile, 'sex')->dropDownList($model->profile->sexList(), ['prompt' => 'Не выбрано', 'class' => 'form-select']) ?>
            </div>
            <div class="col-md-6">
                <?php if (!empty($user->profile) && !empty($user->profile->photo)): ?>
                    <div class="mb-2">
                        <img src="<?= $user->profile->getThumbUrl('photo', 'thumb') ?>" alt="" class="img-fluid">
                    </div>
                <?php endif; ?>
                <?= $form->field($model->profile, 'photo')->fileInput() ?>
            </div>
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <div class="form-group">
            <div class="d-grid">
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
</div>
<!-- /.card -->
<?php ActiveForm::end(); ?>
