<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\backend;

use Besnovatyj\Kernel\controller\ControllerTrait;
use Besnovatyj\User\services\auth\NetworkService;
use Throwable;
use Yii;
use yii\filters\VerbFilter;

use yii\helpers\VarDumper;
use yii\web\Controller;

/**
 * 00000000000000000000000
 */
class NetworkController extends Controller
{
    use ControllerTrait;

    private NetworkService $service;

    public function __construct($id, $module, NetworkService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'detach' => ['post'],
                ],
            ],
        ];
    }

    public function actionDetach($network_id): void
    {
        try {
            $this->service->detach($network_id, Yii::$app->user->id);
            Yii::$app->session->setFlash('success', 'Соц-сеть успешно отвязана.');
        } catch (Throwable $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        $this->goReferer();
    }
}
