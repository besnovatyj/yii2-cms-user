<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\backend;

use Besnovatyj\Kernel\controller\ControllerTrait;
use DomainException;
use Besnovatyj\User\entities\Identity;
use Besnovatyj\User\forms\LoginForm;
use Besnovatyj\User\services\auth\AuthService;
use Yii;
use yii\filters\VerbFilter;

use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\Response;

class AuthController extends Controller
{
    use ControllerTrait;

    private AuthService $authService;

    public function __construct($id, $module, AuthService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->authService = $service;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionLogin(): Response|string
    {// TODO логировать попытки неудачного входа? сохраняя введенные логин и пароль? (удачные пропускать :) )
        if (!Yii::$app->user->isGuest) {
            return $this->goHome(); // TODO - $this->goReferer() ?
        }

        $form = new LoginForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $user = $this->authService->auth($form);
                $rememberMeDuration = $form->rememberMe ? Yii::$app->getModule('user')->params['rememberMeDuration'] : 0;
                Yii::$app->user->login(new Identity($user), $rememberMeDuration);
                return $this->goReferer();
            } catch (DomainException $e) {
                $this->handleDomainException($e);
            }
        }

        return $this->render('login', [
            'model' => $form,
        ]);
    }

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
