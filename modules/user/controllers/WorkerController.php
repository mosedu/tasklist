<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 06.05.2015
 * Time: 13:45
 */

namespace app\modules\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

use app\modules\user\models\User;
use app\modules\user\models\UserSearch;
use yii\web\ForbiddenHttpException;

class WorkerController extends Controller {
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'view', 'delete', 'index', 'update', ],
                        'allow' => true,
                        'roles' => ['createWorker'],
                    ],
                    [
                        'actions' => ['list', ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'changenum' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Department models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->searchWorker(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Department model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if( Yii::$app->request->isAjax ) {
            return $this->renderPartial('view', [
                'model' => $model,
            ]);
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        if( !Yii::$app->user->identity->canCreateWorker() ) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->load(Yii::$app->request->post()) ) {
            if( !Yii::$app->user->can(User::ROLE_ADMIN) ) {
                $model->us_dep_id = Yii::$app->user->identity->us_dep_id;
            }
            // $model->us_role_name = User::ROLE_WORKER;
            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                Yii::warning("Error save worker: " . print_r($model->getErrors(), true));
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Department model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if( !Yii::$app->user->identity->canEditWorker($model) ) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->dep_id]);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Lists wokers for department
     * @param integer $id
     * @return mixed
     */
    public function actionList($id)
    {
        $id = intval($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ($id > 0) ? User::getWorkerList($id) : [];
    }

    /**
     * Finds the Department model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}