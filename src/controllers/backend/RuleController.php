<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\User\controllers\backend;

use Exception;
use Besnovatyj\User\forms\backend\RuleForm;
use Besnovatyj\User\forms\search\RuleSearch;
use Besnovatyj\User\services\manage\RuleManageService;
use Yii;
use yii\filters\VerbFilter;

use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 00000000000000000000000
 */
class RuleController extends \yii\web\Controller
{
    private RuleManageService $service;

    public function __construct($id, $module, RuleManageService $service, $config = [])
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
                    'delete' => ['post'],
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
        $searchModel = new RuleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $model = $this->service->getModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new AuthItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return Response|string
     */
    public function actionCreate(): Response|string
    {
        $form = new \Besnovatyj\User\forms\backend\RuleForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $rule = $this->service->create($form);
                Yii::$app->session->setFlash('success', 'Rule successfully created');
                return $this->redirect(['view', 'id' => $rule->name]);
            } catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                if (YII_DEBUG) {
                    Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка');
                }
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
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
        $rule = $this->service->getModel($id);
        $form = new RuleForm($rule);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $newRule = $this->service->update($form);
                Yii::$app->session->setFlash('success', 'Rule successfully updated');
                return $this->redirect(['view', 'id' => $newRule->name]);
            } catch (Exception $e) {
                Yii::$app->errorHandler->logException($e);
                if (YII_DEBUG) {
                    Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
                } else {
                    Yii::$app->session->setFlash('error', 'Ошибка');
                }
            }
        }
        return $this->render('update', [
            'model' => $form,
        ]);
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
        $rule = $this->service->getModel($id);
        try {
            $this->service->remove($rule);
            Yii::$app->session->setFlash('success', 'Rule successfully deleted');
        } catch (Exception $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->redirect(['index']);
    }
}
