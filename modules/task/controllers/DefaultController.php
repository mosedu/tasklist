<?php

namespace app\modules\task\controllers;

use app\modules\user\models\User;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

use app\modules\task\models\Tasklist;
use app\modules\task\models\TasklistSearch;
use app\rbac\DepartmentRule;
use yii\web\Response;

class DefaultController extends Controller
{
    public $_model = null;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['delete', ],
                        'allow' => true,
                        'roles' => ['createUser'],
                    ],
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'export', 'lastdirect', 'setworker', ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
//                    [
//                        'actions' => ['update', ],
//                        'allow' => true,
//                        'roles' => ['@', ],
//                        'matchCallback' => function ($rule, $action) {
//                            $model = $action->controller->getTasklist();
//                            return Yii::$app->getUser()->can('updateTask', ['task' => $model, ]);
//                            return Yii::$app->getUser()->can('updateDepartTask', ['task' => $model, ]);
//                        },
//                    ],
/*                    [
                        'class' => DepartmentRule::className(),
                        'params' => ['task' => $this->tasklist, ],
                        'actions' => ['update', ],
                        'allow' => true,
                        'roles' => ['updateTask', ],
                    ], */
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
     * Lists all Tasklist models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TasklistSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tasklist model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $oUser = Yii::$app->user->identity;
//        $bDeny = ($model->task_dep_id != $oUser->us_dep_id) && ($oUser->us_dep_id != 1);
/*        $bDeny = ($model->task_dep_id != $oUser->us_dep_id)
            && ( !Yii::$app->user->can(User::ROLE_ADMIN) )
            && ($oUser->department->dep_user_roles != User::ROLE_CONTROL);
*/
//        if( $bDeny ) {
        if( !$model->canEdit() ) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if( Yii::$app->request->isAjax ) {
            return $this->renderPartial('view', [
                'model' => $model,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Tasklist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tasklist();
        $model->setDepartmentByUser();
        if( !Yii::$app->user->can('createTask') ) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->task_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Tasklist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oUser = Yii::$app->user->identity;
//        $bDeny = ($model->task_dep_id != $oUser->us_dep_id) && ($oUser->us_dep_id != 1);
/*        $bDeny = ($model->task_dep_id != $oUser->us_dep_id)
              && ( !Yii::$app->user->can(User::ROLE_ADMIN) )
              && ($oUser->department->dep_user_roles != User::ROLE_CONTROL);
*/
//        if( $bDeny ) {
        if( !$model->canEdit() ) {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->task_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Tasklist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id); // ->delete();
        if( $model !== null ) {
            $model->task_active = Tasklist::STATUS_DELETED;
            $model->save();
        }

        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return mixed
     */
    public function actionSetworker($id)
    {
        $model = $this->findModel($id);
        if( Yii::$app->request->isAjax ) {
            if( $model->load(Yii::$app->request->post()) ) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                if( $model->save() ) {
                    return [];
                }
                else {
                    return $model->getErrors();
                }
            }
            else {
                return $this->renderAjax('selectworker', [
                    'model' => $model,
                ]);

            }
        }
        else {

        }
    }

    /**
     * @return \yii\web\Response
     */
    public function actionLastdirect()
    {
        $aData = Yii::$app
            ->db
            ->createCommand('SELECT Distinct task_direct FROM tlst_tasklist Where task_dep_id = :depid Order By task_id Desc', ['depid'=>Yii::$app->request->get('depid', 0)])
            ->queryColumn();
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $aData;
        return $response;
    }

    /**
     * Экпорт в Excel
     */
    public function actionExport()
    {
        $searchModel = new TasklistSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $format = Yii::$app->request->getQueryParam('format', 'xls');

        $sf = $this->renderPartial(
            'export-excel',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'format' => $format,
            ]
        );

        Yii::info('in controller: ' . $sf);

        if( file_exists($sf) ) {
            Yii::$app->response->sendFile($sf);
        }
        else {
            echo $sf;
//            throw new NotFoundHttpException('The requested export file does not exist.');
        }
    }

    /**
     * Получение модели в проверке прав доступа
     *
     * @return Tasklist the loaded model
     */
    public function getTasklist() {
        list ($route, $params) =  Yii::$app->request->resolve();
        Yii::info('params = ' . print_r($params, true) . "\nroute = " . print_r($route, true));
        Yii::info('this->actionParams = ' . print_r($this->actionParams, true));
        if( isset($params['id']) ) {
            return $this->findModel($params['id']);
        }
    }

    /**
     * Finds the Tasklist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tasklist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if( $this->_model !== null ) {
            return $this->_model;
        }
        $this->_model = Tasklist::find()
            ->where([
                'task_id' => $id,
                'task_active' => Tasklist::STATUS_ACTIVE,
            ])
            ->with('department')
            ->one();

        if ( $this->_model !== null) {
            return $this->_model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
