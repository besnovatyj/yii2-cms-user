<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\backend;

use Besnovatyj\Kernel\controller\ControllerTrait;
use DomainException;
use Besnovatyj\User\forms\backend\PasswordEditForm;
use Besnovatyj\User\forms\backend\UserCreateForm;
use Besnovatyj\User\forms\backend\UserEditForm;
use Besnovatyj\User\forms\backend\UserSearch;
use Besnovatyj\User\repositories\UserRepository;
use Besnovatyj\User\services\manage\UserManageService;
use Throwable;
use Yii;
use yii\base\Exception;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

/**
 * 000000
 */
class UserController extends Controller
{
    use ControllerTrait;

    private UserManageService $service;
    private UserRepository $repo;

    public function __construct($id, $module, UserManageService $service, UserRepository $repo, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->repo = $repo;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'block' => ['POST'],
                    'activate' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id
     * @return string
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->repo->get($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return Response|string
     * @throws Exception
     */
    public function actionCreate(): Response|string
    {
        $form = new UserCreateForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $user = $this->service->create($form);
                return $this->redirect(['view', 'id' => $user->id]);
            } catch (DomainException $e) {
                $this->handleDomainException($e);
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return Response|string
     */
    public function actionUpdate(int $id): Response|string
    {
        $user = $this->repo->get($id);
        $form = new UserEditForm($user);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($user->id, $form);
                return $this->redirect(['view', 'id' => $user->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }
        return $this->render('update', [
            'model' => $form,
            'user' => $user,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * Защита от удаления root-пользователя реализована на уровне UserManageService.
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->service->remove($id); // todo - удалять профайл при удалении юзера
        } catch (Throwable $e) {
            $this->handleDomainException($e);
        }
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing User model password.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return Response|string
     */
    public function actionPasswordUpdate(int $id): Response|string
    {
        $user = $this->repo->get($id);

        $form = new PasswordEditForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->passwordEdit($user->id, $form);
                return $this->redirect(['view', 'id' => $user->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }
        return $this->render('password', [
            'model' => $form,
            'user' => $user,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionActivate(int $id): Response
    {
        try {
            $this->service->activate($id);
            Yii::$app->session->setFlash('success', 'Пользователь разблокирован.');
            return $this->redirect(['/User/backend/user/view', 'id' => $id]);
        } catch (Exception $e) {
            $this->handleDomainException($e);
        }
        return $this->goReferer();
    }

    /**
     * Блокировка пользователя.
     * Защита от блокировки root-пользователя реализована на уровне UserManageService.
     * @param int $id
     * @return Response
     */
    public function actionBlock(int $id): Response
    {
        try {
            $this->service->block($id);
            Yii::$app->session->setFlash('danger', 'Пользователь заблокирован.');
            return $this->redirect(['/User/backend/user/view', 'id' => $id]);
        } catch (Exception $e) {
            $this->handleDomainException($e);
        }
        return $this->goReferer();
    }

    public function actionResetEmailConfirmToken(int $id): Response
    {
        try {
            $this->service->resetEmailConfirmToken($id);
            Yii::$app->session->setFlash('success', 'Токен запроса на смену e-mail удалён.');
        } catch (Exception $e) {
            $this->handleDomainException($e);
        }
        return $this->goReferer();
    }

    public function actionResetPasswordResetToken(int $id): Response
    {
        try {
            $this->service->resetPasswordResetToken($id);
            Yii::$app->session->setFlash('success', 'Токен запроса на смену пароля удалён.');
        } catch (Exception $e) {
            $this->handleDomainException($e);
        }
        return $this->goReferer();
    }
}
