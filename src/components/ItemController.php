<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\components;

use Besnovatyj\User\entities\AuthItem;
use Besnovatyj\User\forms\search\AuthItem as AuthItemSearch;
use Exception;
use Besnovatyj\Helpers\json\Json;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\NotSupportedException;
use yii\filters\VerbFilter;
use yii\rbac\Item;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * AuthItemController implements the CRUD actions for AuthItem model.
 *
 * @property integer $type
 * @property array $labels
 */
class ItemController extends Controller
{
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'assign' => ['post'],
                    'remove' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all AuthItem models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new AuthItemSearch(['type' => $this->type]);
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single AuthItem model.
     * @param string $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(string $id): string
    {
        $model = $this->findModel($id);

        return $this->render('view', ['model' => $model]);
    }

    /**
     * Finds the AuthItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return AuthItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(string $id): AuthItem
    {
        $auth = Yii::$app->getAuthManager();
        $item = $this->type === Item::TYPE_ROLE ? $auth->getRole($id) : $auth->getPermission($id);
        if ($item) {
            return new AuthItem($item);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return Response|string
     * @throws Exception
     */
    public function actionCreate(): Response|string
    {
        $model = new AuthItem(null);
        $model->type = $this->type;
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        } else {
            return $this->render('create', ['model' => $model]);
        }
    }

    /**
     * Updates an existing AuthItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate(string $id): Response|string
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->name]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing AuthItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionDelete(string $id): Response
    {
        $model = $this->findModel($id);
        Yii::$app->getAuthManager()->remove($model->item);
        Helper::invalidate();

        return $this->redirect(['index']);
    }

    /**
     * Assign items
     * @param string $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionAssign(string $id): array
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $items = Json::decode($items);
        $model = $this->findModel($id);
        $success = $model->addChildren($items);
        Yii::$app->getResponse()->format = 'json';

        return array_merge($model->getItems(), ['success' => $success, 'status' => 'success']);
    }

    /**
     * Возвращает список пользователей, которым назначена переданная роль.
     *
     * Принимает название роли и текущую страницу дял пагинации.
     * Данные возвращает соответственно текущей страницы пагинации и переданной роли.
     *
     * @param string $id - Роль для поиска пользователей.
     * @return array - Массив, содержащий массив пользователей (соотв-но страницы пагинации) и данные для ссылок пагинации
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function actionGetUsersByRole(string $id): array
    {
        $page = Yii::$app->getRequest()->get('page', 0);
        $model = $this->findModel($id);
        Yii::$app->getResponse()->format = 'json';
        return array_merge($model->getUsers($page), ['status' => 'success']);
    }

    /**
     * Assign or remove items
     * @param string $id
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionRemove(string $id): array
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $items = Json::decode($items);
        $model = $this->findModel($id);
        $success = $model->removeChildren($items);
        Yii::$app->getResponse()->format = 'json';

        return array_merge($model->getItems(), ['success' => $success, 'status' => 'success']);
    }

    public function getViewPath(): string
    {
        return $this->module->getViewPath() . DIRECTORY_SEPARATOR . 'backend/item';
    }

    /**
     * Label use in view
     * Переопределяется в PermissionController и RoleController
     *
     * @throws NotSupportedException
     */
    public function labels()
    {
        throw new NotSupportedException(get_class($this) . ' does not support labels().');
    }

    /**
     * Type of Auth Item.
     * Переопределяется в PermissionController и RoleController
     *
     * @return integer
     * @throws NotSupportedException
     */
    public function getType(): int
    {
        throw new NotSupportedException(get_class($this) . ' does not support getType().');
    }
}
