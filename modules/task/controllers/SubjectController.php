<?php

namespace app\modules\task\controllers;

use Yii;
use app\modules\task\models\Subject;
use app\modules\task\models\SubjectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\widgets\ActiveForm;
use yii\web\Response;

/**
 * SubjectController implements the CRUD actions for Subject model.
 */
class SubjectController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'import', 'delete'],
                        'allow' => true,
                        'roles' => ['createUser'],
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
     * Lists all Subject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SubjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Subject model.
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
     * Creates a new Subject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->actionUpdate(0);
        /*
        $model = new Subject();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->subj_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
        */
    }

    /**
     * Updates an existing Subject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if( $id == 0 ) {
            $model = new Subject();
        }
        else {
            $model = $this->findModel($id);
        }

        if( Yii::$app->request->isAjax ) {
            if( $model->load(Yii::$app->request->post()) ) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $aValidate = ActiveForm::validate($model);
                if( count($aValidate) == 0 ) {
                    if( !$model->save() ) {
                        $s = 'Error save Resource: ' . print_r($model->getErrors(), true);

                        Yii::info($s);
                        Yii::error($s);
                    }
                }
                return $aValidate;
            }
            else {
                return $this->renderAjax(
                    '_form',
                    [
                        'model' => $model,
                    ]
                );
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->subj_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Subject model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id); // ->delete();
        $model->subj_is_active = $model->subj_is_active == 1 ? 0 : 1;
        if( !$model->save() ) {
            Yii::info("Error delete subject: " . print_r($model->getErrors(), true) . "\nmodel = " . print_r($model->attributes, true));
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Subject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Subject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Subject::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
