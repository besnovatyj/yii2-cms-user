<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Backend\Widgets\grid\ActionColumn;
use Besnovatyj\Backend\Widgets\pagination\LinkPager;
use Besnovatyj\DateTime\DateTimeRangeWidget;
use Besnovatyj\User\components\Helper;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\backend\UserSearch;
use Besnovatyj\User\helpers\UserHelper;
use Besnovatyj\User\widgets\grid\RoleColumn;
use yii\bootstrap5\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\web\View;

/* @var $this View */
/* @var $searchModel UserSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('rbac-admin', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
</p>

<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body table-responsive">
        <?= GridView::widget([
            'options' => ['class' => 'table detail-view'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}",
            'columns' => [
                'id',
                [
                    'attribute' => 'created_at',
                    'format' => 'datetime',
                    'filter' => DateTimeRangeWidget::widget([
                        'model' => $searchModel,
                        'attributeFrom' => 'date_from',
                        'attributeTo' => 'date_to',
                    ]),
                ],
                [
                    'attribute' => 'username',
                    'value' => function (User $model) {
                        $description = empty($model->description) ? '' : ' <small><i class="bi bi-question-circle-fill text-secondary" title="' . $model->description . '"></i></small>';
                        return Html::a('👁'.Html::encode($model->username), ['view', 'id' => $model->id]) . $description;
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'email',
                    'value' => function (User $model) {
                        return Html::mailto('📧' . Html::encode($model->email));
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'role',
                    'class' => RoleColumn::class,
                    'filter' => $searchModel->rolesList(),
                ],
                [
                    'attribute' => 'status',
                    'filter' => UserHelper::statusList(),
                    'value' => function (User $model) {
                        return UserHelper::statusLabel($model);
                    },
                    'format' => 'raw',
                ],
                ['class' => ActionColumn::class,
                    'template' => Helper::filterActionColumn('{view} {update} {delete}'),
                ],
            ],
        ]); ?>
    </div>
    <div class="card-footer clearfix">
        <nav aria-label="" class="nav-pagination">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->getPagination(),
            ]) ?>
        </nav>
    </div>
</div>
