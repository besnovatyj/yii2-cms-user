<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\backend;

use Exception;
use Besnovatyj\Helpers\json\Json;
use Besnovatyj\User\services\manage\RouteManageService;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * Управление маршрутами RBAC.
 *
 * Ajax-экшены (create/assign/remove/refresh) отдают JSON; обработка ошибок делегирована
 * {@see \yii\web\ErrorHandler}: инфраструктурные исключения сервиса всплывают к нему (в проде
 * детали скрыты, в debug видны). Ответ об ошибке — реальный HTTP-статус + нативное тело Yii;
 * успех — конверт приложения `{status:'success', ...}`. HTML-экшен index обрабатывает ошибку сам
 * (flash-сообщение), т.к. рендерит страницу.
 */
class RouteController extends Controller
{
    private RouteManageService $service;

    public function __construct($id, $module, RouteManageService $service, $config = [])
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
                    'create' => ['post'],
                    'assign' => ['post'],
                    'remove' => ['post'],
                    'refresh' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $routes = [];
        try {
            $routes = $this->service->getRoutes();
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->render('index', ['routes' => $routes]);
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionCreate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->getRequest()->getIsAjax()) {
            throw new BadRequestHttpException('Ожидается AJAX-запрос.');
        }

        // Строка маршрутов, разделённая запятыми
        $routes = Yii::$app->getRequest()->post('route', '');

        return [
            'status' => 'success',
            'data' => $this->service->create($routes),
            'message' => 'Routes successfully created!',
        ];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionAssign(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->getRequest()->getIsAjax()) {
            throw new BadRequestHttpException('Ожидается AJAX-запрос.');
        }

        $routes = Json::decode(Yii::$app->getRequest()->post('routes', []));

        return [
            'status' => 'success',
            'data' => $this->service->assign($routes),
            'message' => 'Assignments successfully updated!',
        ];
    }

    /**
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionRemove(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->getRequest()->getIsAjax()) {
            throw new BadRequestHttpException('Ожидается AJAX-запрос.');
        }

        $routes = Json::decode(Yii::$app->getRequest()->post('routes', []));

        return [
            'status' => 'success',
            'data' => $this->service->remove($routes),
            'message' => 'Assignments successfully updated!',
        ];
    }

    /**
     * Refresh cache
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionRefresh(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->getRequest()->getIsAjax()) {
            throw new BadRequestHttpException('Ожидается AJAX-запрос.');
        }

        return [
            'status' => 'success',
            'data' => $this->service->getRoutes(true),
            'message' => 'Список маршрутов обновлён',
        ];
    }
}
