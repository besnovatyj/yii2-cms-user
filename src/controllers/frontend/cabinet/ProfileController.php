<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\frontend\cabinet;

use DomainException;
use Exception;
use Besnovatyj\User\entities\User;
use Besnovatyj\User\forms\frontend\UserEditForm;
use Besnovatyj\User\services\cabinet\ProfileService;
use Yii;
use yii\filters\AccessControl;

use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Работа с профилем пользователя
 */
class ProfileController extends \yii\web\Controller
{

    private $service;

    public function __construct($id, $module, ProfileService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @throws NotFoundHttpException|\Throwable
     */
    public function actionEdit(): Response|string
    {
        $user = $this->findModel(Yii::$app->user->id);

        $form = new UserEditForm($user);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $emailChanged = $this->service->edit($user->id, $form);
                if ($emailChanged) {
                    Yii::$app->session->setFlash('success', 'Запрос на смену email отправлен. Пожалуйста, проверьте свою почту.');
                }
                return $this->redirect(['/User/cabinet/default/index']);
            } catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                if (YII_DEBUG) {
                    Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка');
                }
            }
        }
        if ($form->hasErrors()) {
            $errors = $form->getErrorSummary(true);
            Yii::$app->session->addFlash('error', $errors);
        }
        return $this->render('/frontend/user/profile/edit', [
            'model' => $form,
            'user' => $user,
        ]);
    }

    public function actionConfirmPhone($token): Response
    {
        try {
            $this->service->confirmPhoneChange(\Yii::$app->user->id, $token);
            Yii::$app->session->setFlash('success', 'Номер телефона изменён.');
            return $this->redirect(['/User/cabinet/default/index']);
        } catch (DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->goHome();
    }

    public function actionConfirmEmail($token): Response
    {
        try {
            $this->service->confirmEmail($token);
            Yii::$app->session->setFlash('success', 'E-mail изменён.');
            return $this->redirect(['/User/cabinet/default/index']);
        } catch (DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->goHome();
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): User
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
