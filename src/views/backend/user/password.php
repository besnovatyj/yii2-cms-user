<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\backend\UserEditForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model UserEditForm */
/* @var $user User */

if (!empty($user->username)) {
    $userTitle = $user->username;
} elseif (!empty($user->email)) {
    $userTitle = $user->email;
} else {
    $userTitle = $user->id;
}

$this->title = 'Update password: ' . $userTitle;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $userTitle, 'url' => ['view', 'id' => $user->id]];
$this->params['breadcrumbs'][] = 'Update password';

$this->registerJs(file_get_contents(__DIR__ . '/password_show_hide.js'), View::POS_END);
?>

<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-header"><?= $userTitle ?></div>
    <div class="card-body">
        <?= $form->field($model, 'password', ['template' => "
        {label}
        <div class='input-group' id='show_hide_password'>
            {input}
            <div class='input-group-text bg-success text-white' style='cursor:pointer;'>
                <span><i class='bi bi-eye-slash'></i></span>
            </div>
        </div>
        {error}\n
        {hint}\n
        "])->passwordInput(['class' => 'form-control']) ?>
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
