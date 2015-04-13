<?php

namespace app\modules\task\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

use app\modules\task\models\Tasklist;
use app\modules\task\models\TasklistSearch;
use app\rbac\DepartmentRule;

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
                        'actions' => ['view', 'delete', ],
                        'allow' => true,
                        'roles' => ['createUser'],
                    ],
                    [
                        'actions' => ['index', 'create', ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['update', ],
                        'allow' => true,
                        'roles' => ['updateTask', ],
/*                        'matchCallback' => function ($rule, $action) {
                            $model = $action->controller->getTasklist();
                            return Yii::$app->getUser()->can('updateDepartTask', ['task' => $model, ]);
                        },
*/
                    ],
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
