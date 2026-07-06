<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\frontend\cabinet;

use Besnovatyj\User\repositories\NotFoundException;
use Besnovatyj\User\repositories\UserReadRepository;
use Yii;
use yii\filters\AccessControl;


/**
 * Главная страница личного кабинета
 */
class DefaultController extends \yii\web\Controller
{

    private $repo;

    public function __construct($id, $module, UserReadRepository $repo, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->repo = $repo;
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
     * @return string
     * @throws NotFoundException
     */
    public function actionIndex(): string
    {
//        if (!\Yii::$app->getUser()->identity->isProfileCorrectlyFilled()) {
//            \Yii::$app->session->addFlash('error', 'Пожалуйста, заполните свой профиль!');
//            $this->redirect(['/User/cabinet/profile/edit']);
//        }

        $user = $this->repo->getActiveById(Yii::$app->user->id);
        return $this->render('/frontend/user/cabinet/index', [
            'user' => $user,
        ]);
    }
}
