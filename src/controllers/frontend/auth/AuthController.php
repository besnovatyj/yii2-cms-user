<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\frontend\auth;

use Besnovatyj\Kernel\controller\ControllerTrait;
use Exception;
use Besnovatyj\User\entities\Identity;
use Besnovatyj\User\forms\LoginForm;
use Besnovatyj\User\services\auth\AuthService;
use Yii;
use yii\web\Controller;
use yii\web\Response;

/**
 * Аутентификация и разлогивание
 */
class AuthController extends Controller
{
    use ControllerTrait;

    private AuthService $service;

    public function __construct($id, $module, AuthService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    /**
     * Аутентифицирует пользователя
     * @return string|Response
     */
    public function actionLogin(): Response|string
    {
        // TODO логировать попытки неудачного входа? сохраняя введенные логин и пароль? (удачные пропускать :) )
        // goBack() фреймворка
        //
        //  return $this->response->redirect(Yii::$app->getUser()->getReturnUrl($defaultUrl));
        //
        //  Редиректит на returnUrl из компонента User — это внутреннее состояние приложения (хранится в сессии), а не заголовок браузера. Этот URL вы сами раньше где-то
        //  выставили через Yii::$app->user->setReturnUrl() либо он выставился автоматически (например, loginRequired() запомнил, куда пользователь хотел попасть до
        //  редиректа на логин).
        //
        //  - Это не «предыдущая страница», а «куда вернуться после действия» — семантика логин/авторизация.
        //  - Если returnUrl не задан — уходит на defaultUrl, а если и его нет — на homeUrl. Безопасный фолбэк всегда есть.
        if (!Yii::$app->user->isGuest) {
            return $this->goBack();
        }

        $form = new LoginForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $user = $this->service->auth($form);
                $rememberMeDuration = $form->rememberMe ? Yii::$app->getModule('user')->params['rememberMeDuration'] : 0;
                Yii::$app->user->login(new Identity($user), $rememberMeDuration);
                Yii::$app->session->addFlash('success', 'Вы успешно вошли в свой аккаунт');
                return $this->goBack();
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }
        if ($form->hasErrors()) {
            $errors = $form->getErrorSummary(true);
            Yii::$app->session->addFlash('error', $errors);
        }
        return $this->render('/frontend/user/auth/login', [
            'model' => $form,
        ]);
    }

    /**
     * Разлогинивает пользователя
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
