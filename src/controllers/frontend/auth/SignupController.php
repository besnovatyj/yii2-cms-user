<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\frontend\auth;

use DomainException;
use Besnovatyj\User\forms\frontend\SignupForm;
use Besnovatyj\User\services\auth\SignupService;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;

use yii\helpers\VarDumper;
use yii\web\Response;

/**
 * Регистрация нового пользователя
 */
class SignupController extends \yii\web\Controller
{
    private $service;

    public function __construct($id, $module, \Besnovatyj\User\services\auth\SignupService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['request'],
                'rules' => [
                    [
                        'actions' => ['request'],
                        'allow' => true,
                        'roles' => ['?'], // `?` - все гости
                    ],
                ],
            ],
        ];
    }

    /**
     * Запрос на регистрацию
     * @return Response|string
     * @throws Exception
     */
    public function actionRequest(): \yii\web\Response|string
    {
        $form = new SignupForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->signup($form);
                Yii::$app->session->setFlash('success', 'Ссылка для подтверждения регистрации выслана на указанный Вами адрес электронной почты.');
                return $this->redirect(['/user/auth/auth/login']);
            } catch (DomainException $e) {
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
        return $this->render('/frontend/user/signup/request', [
            'model' => $form,
        ]);
    }

    /**
     * Подтверждение регистрации
     * @param $token
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionConfirm($token): Response
    {
        try {
            $this->service->confirm($token);
            Yii::$app->session->setFlash('success', 'Ваш аккаунт подтверждён.');
            return $this->redirect(['/user/auth/auth/login']);
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
}
