<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\backend;

use Besnovatyj\Helpers\json\Json;
use Besnovatyj\User\forms\search\AssignmentSearch;
use Besnovatyj\User\services\manage\AssignmentManageService;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * Управление назначениями (роли/разрешения) пользователю.
 *
 * Ajax-экшены (assign/revoke) отдают JSON; обработка ошибок делегирована {@see \yii\web\ErrorHandler}:
 * инфраструктурные исключения сервиса НЕ ловятся здесь и всплывают к нему (в проде детали скрыты,
 * в debug видны). Ответ об ошибке — реальный HTTP-статус + нативное тело Yii (`{name,message,code,status}`);
 * успех — конверт приложения `{status:'success', ...}`.
 */
class AssignmentController extends Controller
{
    private AssignmentManageService $service;

    public function __construct($id, $module, AssignmentManageService $service, $config = [])
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
                    'assign' => ['post'],
                    'revoke' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Assignment models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new AssignmentSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Assignment model.
     * @param int $id - User Id
     * @return string
     * @throws Throwable
     */
    public function actionView(int $id): string
    {
        $user = $this->service->getUser($id);
        $assignments = $this->service->getItems($user->id);

        return $this->render('view', [
            'assignments' => $assignments,
            'user_id' => $user->id,
            'username' => $user->username,
        ]);
    }

    /**
     * Assign items
     * @param int $id - User Id
     * @return array
     * @throws BadRequestHttpException
     * @var array $items - Roles | Permissions combined array
     */
    public function actionAssign(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->getRequest()->getIsAjax()) {
            throw new BadRequestHttpException('Ожидается AJAX-запрос.');
        }

        $items = array_filter(Json::decode(Yii::$app->getRequest()->post('items', [])));

        return [
            'status' => 'success',
            'data' => $this->service->assign($id, $items),
            'message' => 'Assignments successfully updated!',
        ];
    }

    /**
     * Revoke items
     * @param int $id - User Id
     * @return array
     * @throws BadRequestHttpException
     * @var array $items - Roles | Permissions combined array
     */
    public function actionRevoke(int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->getRequest()->getIsAjax()) {
            throw new BadRequestHttpException('Ожидается AJAX-запрос.');
        }

        $items = array_filter(Json::decode(Yii::$app->getRequest()->post('items', [])));

        return [
            'status' => 'success',
            'data' => $this->service->revoke($id, $items),
            'message' => 'Assignments successfully updated!',
        ];
    }
}
