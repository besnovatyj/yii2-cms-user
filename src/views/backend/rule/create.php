<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\User\forms\backend\RuleForm;
use yii\web\View;

/* @var $this  View */
/* @var $model RuleForm */

$this->title = Yii::t('rbac-admin', 'Create Rule');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac-admin', 'Rules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= $this->render('_form', [
            'model' => $model,
        ]); ?>
    </div>
    <div class="card-footer clearfix"></div>
</div>
