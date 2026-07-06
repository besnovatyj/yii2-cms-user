<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\frontend\auth;

use DomainException;
use Exception;
use Besnovatyj\User\forms\frontend\PasswordResetRequestForm;
use Besnovatyj\User\forms\frontend\ResetPasswordForm;
use Besnovatyj\User\services\auth\PasswordResetService;
use Yii;

use yii\helpers\VarDumper;
use yii\web\Response;

/**
 * Сброс пароля
 */
class ResetController extends \yii\web\Controller
{
    private $service;

    public function __construct($id, $module, PasswordResetService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * Запрос на смену пароля
     */
    public function actionRequest(): Response|string
    {
        $form = new PasswordResetRequestForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->request($form);
                Yii::$app->session->setFlash('success', 'На вашу почту выслана инструкция для смены пароля.');
                return $this->redirect(['/user/auth/auth/login']);
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
        return $this->render('/frontend/user/reset/request', [
            'model' => $form,
        ]);
    }

    /**
     * Подтверждение сброса пароля
     * @param string $token
     * @return Response|string
     */
    public function actionConfirm(string $token): Response|string
    {
        if (!$this->service->checkToken($token)) {
            $this->goHome();
        }

        $form = new ResetPasswordForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->reset($token, $form);
                Yii::$app->session->setFlash('success', 'Новый пароль успешно сохранён.');
                // TODO если сохранили новый пароль, то можно сразу авторизовать с ним пользователя
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
        if ($form->hasErrors()) {
            $errors = $form->getErrorSummary(true);
            Yii::$app->session->addFlash('error', $errors);
        }
        return $this->render('/frontend/user/reset/confirm', [
            'model' => $form,
        ]);
    }
}
