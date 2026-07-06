<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\entities\User;
use Besnovatyj\User\helpers\UserHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model User */

$this->title = 'User - ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$networkList = function () use ($model) {
    $out = '<table class="table table-bordered">';
    foreach ($model->networks as $network) {
        $link = Html::a('Открепить', ['/user/backend/network/detach', 'network_id' => $network->id], ['data-method' => 'POST']);
        $out .= '<tr><td>' . $network->network . $network->identity . '</td><td>' . $link . '</td></tr>';
    }
    $out .= '</table>';
    return $out;
};


?>

<p>
    <?= Html::a(Yii::t('rbac-admin', 'Update profile'), ['update', 'id' => $model->id], ['class' => 'btn  btn-success']) ?>

    <?= Html::a(Yii::t('rbac-admin', 'Role assignments'), ['/user/backend/assignment/view', 'id' => $model->id], ['class' => 'btn  btn-warning']) ?>

    <?= Html::a('Update password', ['password-update', 'id' => $model->id], ['class' => 'btn  btn-warning']) ?>
    <?php if ($model->isActive()): ?>
        <?= Html::a(Yii::t('rbac-admin', 'Block'), ['block', 'id' => $model->id], [
            'class' => 'btn  btn-danger',
            'data' => [
                'confirm' => 'Блокировать пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    <?php elseif ($model->isBlocked()): ?>
        <?= Html::a(Yii::t('rbac-admin', 'Activate'), ['activate', 'id' => $model->id], [
            'class' => 'btn  btn-danger',
            'data' => [
                'confirm' => 'Активировать пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    <?php endif; ?>
    <?= Html::a(Yii::t('rbac-admin', 'Delete'), ['delete', 'id' => $model->id], [
        'class' => 'btn  btn-danger',
        'data' => [
            'confirm' => 'Удалить пользователя?',
            'method' => 'post',
        ],
    ]) ?>
</p>

<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'username',
                'email:email',
                'phone',
                [
                    'attribute' => 'status',
                    'value' => UserHelper::statusLabel($model),
                    'format' => 'raw',
                ],
                [
                    'label' => 'Role',
                    'value' => implode(', ', ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($model->id), 'description')),
                    'format' => 'raw',
                ],
                'description',
                'created_at:datetime',
                'updated_at:datetime',
                [
                    'attribute' => 'email_confirm_token',
                    'value' => function (User $model) {
                        if ($model->email_confirm_token) {
                            return Html::a('<span class="btn btn-sm btn-warning">Reset</span>', \yii\helpers\Url::to(['/user/backend/user/reset-email-confirm-token', 'id' => $model->id]));
                        }
                        return $model->email_confirm_token;
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'password_reset_token',
                    'value' => function (User $model) {
                        if ($model->password_reset_token) {
                            return Html::a('<span class="btn btn-sm btn-warning">Reset</span>', \yii\helpers\Url::to(['/user/backend/user/reset-password-reset-token', 'id' => $model->id]));
                        }
                        return $model->password_reset_token;
                    },
                    'format' => 'html',
                ],
                [
                    'label' => 'Соц-сети',
                    'value' => $networkList,
                    'format' => 'raw',
                ],
            ],
        ]) ?>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">

    </div>
</div>
<!-- /.card -->
