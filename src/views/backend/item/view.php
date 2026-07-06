<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Helpers\json\Json;
use Besnovatyj\User\assets\AnimateAsset;
use Besnovatyj\User\components\ItemController;
use Besnovatyj\User\entities\AuthItem;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model AuthItem */
/* @var $context ItemController */

$context = $this->context;
$labels = $context->labels();
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', $labels['Items']), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

AnimateAsset::register($this);
YiiAsset::register($this);
$opts = [
    'items' => $model->getItems(),
    'users' => $model->getUsers(),
    'getUserUrl' => Url::to(['get-users-by-role', 'id' => $model->name])
];
$this->registerJs(file_get_contents(__DIR__ . '/_script.js'), $this::POS_END);
$animateIcon = ' <i class="bi bi-arrow-repeat animate"></i>';
?>

<p>
    <?= Html::a(Yii::t('rbac-admin', 'Update'), ['update', 'id' => $model->name], ['class' => 'btn btn-primary']); ?>
    <?=
    Html::a(Yii::t('rbac-admin', 'Delete'), ['delete', 'id' => $model->name], [
        'class' => 'btn btn-danger',
        'data-confirm' => Yii::t('rbac-admin', 'Are you sure to delete this item?'),
        'data-method' => 'post',
    ]);
    ?>
    <?= Html::a(Yii::t('rbac-admin', 'Create'), ['create'], ['class' => 'btn btn-success']); ?>
</p>
<div class="card">
    <div class="card-header">Общая информация</div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'description:ntext',
                'ruleName',
                'data:ntext',
            ],
            'template' => '<tr><th style="width:25%">{label}</th><td>{value}</td></tr>',
        ]); ?>
        <div class="row mt-3">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered">
                    <tbody>
                    <tr>
                        <th><?= Yii::t('rbac-admin', 'Assigned users'); ?></th>
                    </tr>
                    <tr>
                        <td id="list-users"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer clearfix"></div>
</div>

<div class="card">
    <div class="card-header">Назначения</div>
    <div class="card-body" id="assignment-block" data-opts='<?= Json::encode($opts) ?>'>
        <div class="row">
            <div class="col-sm-5">
                <input class="form-control search" data-target="available"
                       placeholder="<?= Yii::t('rbac-admin', 'Search for available'); ?>">
                <select multiple size="20" class="form-control list" data-target="available"></select>
            </div>
            <div class="col-sm-1 text-center mt-5">
                <?=
                Html::a('<i class="bi bi-chevron-double-right"></i>' . $animateIcon, ['assign', 'id' => $model->name], [
                    'class' => 'btn  btn-success btn-assign',
                    'data-target' => 'available',
                    'title' => Yii::t('rbac-admin', 'Assign'),
                ]);
                ?><br><br>
                <?=
                Html::a('<i class="bi bi-chevron-double-left"></i>' . $animateIcon, ['remove', 'id' => $model->name], [
                    'class' => 'btn  btn-danger btn-assign',
                    'data-target' => 'assigned',
                    'title' => Yii::t('rbac-admin', 'Remove'),
                ]);
                ?>
            </div>
            <div class="col-sm-5">
                <input class="form-control search" data-target="assigned"
                       placeholder="<?= Yii::t('rbac-admin', 'Search for assigned'); ?>">
                <select multiple size="20" class="form-control list" data-target="assigned"></select>
            </div>
        </div>
    </div>
    <div class="card-footer clearfix"></div>
</div>
