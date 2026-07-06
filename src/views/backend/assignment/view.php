<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\assets\AnimateAsset;
use yii\bootstrap5\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\web\YiiAsset;

/* @var $this View */
/* @var $assignments array */
/* @var $username string */
/* @var $user_id int */

$userName = Html::encode($username);

$this->title = Yii::t('rbac-admin', 'Assignment') . ' : ' . $userName;

$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $userName;

AnimateAsset::register($this);
YiiAsset::register($this);
$this->registerJs(file_get_contents(__DIR__ . '/_script.js'), $this::POS_END);
$animateIcon = ' <i class="bi bi-arrow-repeat animate"></i>';
?>
<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <div class="row" id="asgmts-block" data-opts='<?= Json::encode($assignments) ?>'>
            <div class="col-sm-5">
                <input class="form-control search" data-target="available"
                       placeholder="<?= Yii::t('rbac-admin', 'Search for available'); ?>">
                <select multiple size="20" class="form-control list" data-target="available">
                </select>
            </div>
            <div class="col-sm-1 text-center mt-5">
                <?= Html::a('<i class="bi bi-chevron-double-right"></i>' . $animateIcon, ['assign', 'id' => $user_id], [
                    'class' => 'btn btn-success  btn-assign',
                    'data-target' => 'available',
                    'title' => Yii::t('rbac-admin', 'Assign'),
                ]); ?><br><br>
                <?= Html::a('<i class="bi bi-chevron-double-left"></i>' . $animateIcon, ['revoke', 'id' => $user_id], [
                    'class' => 'btn btn-danger  btn-assign',
                    'data-target' => 'assigned',
                    'title' => Yii::t('rbac-admin', 'Remove'),
                ]); ?>
            </div>
            <div class="col-sm-5">
                <input class="form-control search" data-target="assigned"
                       placeholder="<?= Yii::t('rbac-admin', 'Search for assigned'); ?>">
                <select multiple size="20" class="form-control list" data-target="assigned">
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer clearfix">

    </div>
</div>
