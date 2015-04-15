<?php

namespace app\modules\user\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

use app\modules\user\models\Department;
use app\modules\user\models\DepartmentSearch;

/**
 * DepartmentController implements the CRUD actions for Department model.
 */
class DepartmentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'view', 'delete', 'index', 'update', 'changenum', ],
                        'allow' => true,
                        'roles' => ['createUser'],
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
        $searchModel = new DepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Department model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Department();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->dep_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->dep_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Department model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        // ->delete();
        if( $model ) {
            $model->dep_active = $model->dep_active === Department::STATUS_ACTIVE ? Department::STATUS_DELETED : Department::STATUS_ACTIVE;
            if( !$model->save() ) {
                Yii::warning('Department::actionDelete('.$id.') ERROR : ' . print_r($model->getErrors(), true));
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Меняем dep_num у 2 отделов
     * @return mixed
     */
    public function actionChangenum()
    {
        $request = Yii::$app->request;
        $id = $request->post('id', 0);
        $num = $request->post('num', 0);
        $up = $request->post('up', 0);
        $err = null;
        try{
            $model = $this->findModel($id);
        }
        catch(Exception $e) {
            $err = "Error: " . Exception;
            $model = null;
        }

        if( $model !== null ) {
            if( $up == 1 ) {
                $prevId = $model->getPrevByNum();
                $otherModel = Department::findOne($prevId);
            }
            else {
                $nextId = $model->getNextByNum();
                $otherModel = Department::findOne($nextId);
            }
            $nUpd = 0;
            if( $otherModel !== null ) {
                $sSql = 'Update ' . Department::tableName()
                      . ' Set dep_num = IF(dep_id = :id, :other_num, :num)'
                      . ' Where dep_id In (:id, :other_id)';

                $nUpd = Yii::$app->db->createCommand(
                    $sSql,
                    [
                        ':id' => $model->dep_id,
                        ':num' => $model->dep_num,
                        ':other_id' => $otherModel->dep_id,
                        ':other_num' => $otherModel->dep_num,
                    ]
                )
                ->execute();
            }
            else {
                $err = 'Not found other department: ' . (isset($prevId) ? " prev: {$prevId}" : '') . (isset($nextId) ? " next: {$nextId}" : '');
            }
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return [
            'id' => $id,
//            'num' => $num,
            'up' => $up,
            'err' => $err,
            'update' => $nUpd,
        ];
    }

    /**
     * Finds the Department model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Department the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Department::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
