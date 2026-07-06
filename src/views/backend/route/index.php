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
/* @var $routes [] */

$this->title = Yii::t('rbac-admin', 'Routes');
$this->params['breadcrumbs'][] = $this->title;

AnimateAsset::register($this);
YiiAsset::register($this);
//$opts = Json::htmlEncode([
//    'routes' => $routes,
//]);
//$this->registerJs("var _opts = {$opts};");
//$this->registerJs($this->render('_script.js'));
$this->registerJs(file_get_contents(__DIR__ . '/_script.js'), $this::POS_END);
$animateIcon = ' <i class="bi bi-arrow-repeat animate"></i>';
?>

<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body" id="route-block" data-opts='<?= Json::encode($routes) ?>'>
        <div class="row">
            <div class="col-sm-11">
                <div class="input-group">
                    <input id="inp-route" type="text" class="form-control"
                           placeholder="<?= Yii::t('rbac-admin', 'New route(s)'); ?>">
                    <div class="input-group-text bg-success btn" id="btn-new"
                         data-url="<?= \yii\helpers\Url::to(['create']) ?>">
                        <?= Html::a(Yii::t('rbac-admin', 'Add') . $animateIcon, ['create'], ['class' => 'text-white text-decoration-none']); ?>
                    </div>
                </div>
            </div>
        </div>
        <p>&nbsp;</p>
        <div class="row">
            <div class="col-sm-5">
                <div class="input-group">
                    <input class="form-control search" data-target="available"
                           placeholder="<?= Yii::t('rbac-admin', 'Search for available'); ?>">
                    <div class="input-group-text bg-secondary btn " id="btn-refresh"
                         data-url="<?= \yii\helpers\Url::to(['refresh']) ?>">
                        <?= Html::a('<i class="bi bi-arrow-repeat text-white"></i>', ['refresh']); ?>
                    </div>
                </div>
                <select multiple size="20" class="form-control list" data-target="available"></select>
            </div>
            <div class="col-sm-1 text-center mt-5">
                <?= Html::a('<i class="bi bi-chevron-double-right"></i>' . $animateIcon, ['assign'], [
                    'class' => 'btn btn-success  btn-assign',
                    'data-target' => 'available',
                    'title' => Yii::t('rbac-admin', 'Assign'),
                ]); ?><br><br>
                <?= Html::a('<i class="bi bi-chevron-double-left"></i>' . $animateIcon, ['remove'], [
                    'class' => 'btn btn-danger  btn-assign',
                    'data-target' => 'assigned',
                    'title' => Yii::t('rbac-admin', 'Remove'),
                ]); ?>
            </div>
            <div class="col-sm-5">
                <input class="form-control search" data-target="assigned"
                       placeholder="<?= Yii::t('rbac-admin', 'Search for assigned'); ?>">
                <select multiple size="20" class="form-control list" data-target="assigned"></select>
            </div>
        </div>
    </div>
    <div class="card-footer clearfix">
    </div>
</div>
