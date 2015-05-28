<?php

namespace app\modules\task\controllers;

use Yii;
use app\modules\task\models\Requestmsg;
use app\modules\task\models\RequestmsgSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use yii\web\Response;

/**
 * RequestmsgController implements the CRUD actions for Requestmsg model.
 */
class RequestmsgController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['update', 'index', 'view', 'delete', ],
                        'allow' => true,
                        'roles' => ['createUser'],
                    ],
                    [
                        'actions' => ['create', ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Requestmsg models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RequestmsgSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Requestmsg model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Requestmsg model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->actionUpdate(0);
        /*
        $model = new Requestmsg();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->req_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
        */
    }

    /**
     * Updates an existing Requestmsg model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if( $id == 0 ) {
            $model = new Requestmsg();
            $sForm = '_requestdate';
            $model->req_task_id = Yii::$app->request->getQueryParam('taskid', 0);
        }
        else {
            $model = $this->findModel($id);
            $sForm = '_commit';
        }
        $task = $model->task;
        if( $task === null  ) {
            $sForm = '_notask';
        }

        if( Yii::$app->request->isAjax ) {
            if( $model->load(Yii::$app->request->post()) ) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $aValidate = ActiveForm::validate($model);
                if( count($aValidate) == 0 ) {
                    if( !$model->save() ) {
                        $s = 'Error save Requestmsg: ' . print_r($model->getErrors(), true);

                        Yii::info($s);
                        Yii::error($s);
                    }
                }
                return $aValidate;
            }
            else {
                return $this->renderAjax(
                    $sForm,
                    [
                        'model' => $model,
                    ]
                );
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index', ]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'form' => $sForm,
            ]);
        }
    }

    /**
     * Deletes an existing Requestmsg model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Requestmsg model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Requestmsg the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Requestmsg::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
