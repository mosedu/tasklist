<?php

namespace app\modules\task\controllers;

use app\modules\task\models\AskForm;
use app\modules\user\models\User;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use mosedu\multirows\MultirowsBehavior;

use app\modules\task\models\Tasklist;
use app\modules\task\models\TasklistSearch;
use app\modules\task\models\File;
use app\rbac\DepartmentRule;
use yii\web\Response;
use app\modules\task\models\Subject;

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
                        'actions' => ['delete', 'undelete', ],
                        'allow' => true,
                        'roles' => ['createUser'],
                    ],
                    [
                        'actions' => ['view', 'index', 'create', 'update', 'export', 'lastdirect', 'setworker', 'validatetask', 'deltimeshift'],
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

            'validateFiles' => [
                'class' => MultirowsBehavior::className(),
                'model' => File::className(),
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
        // Кусочек по сбросу и сохранению настроек фильтрации
        $aQuery = Yii::$app->request->queryParams;
        if( isset($aQuery['reset']) ) {
            unset(Yii::$app->session['TasklistSearch']);
            unset($aQuery['TasklistSearch']);
        }

        if( isset($aQuery['TasklistSearch']) ) {
            if( isset($aQuery['TasklistSearch']['task_progress']) ) {
                Yii::$app->session['TasklistSearch'] = $aQuery['TasklistSearch'];
            }
            else if( Yii::$app->session->has('TasklistSearch') ) {
                Yii::$app->session['TasklistSearch'] = array_merge(Yii::$app->session['TasklistSearch'], $aQuery['TasklistSearch']);
            }
        }
        else if( Yii::$app->session->has('TasklistSearch') ) {
            $aQuery['TasklistSearch'] = Yii::$app->session['TasklistSearch'];
        }
        $dataProvider = $searchModel->search($aQuery);

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
/*
        if( Yii::$app->request->isAjax && $model->load(Yii::$app->request->post()) ) {
            Yii::info("Load from POST: " . print_r($model->attributes, true));
            $model->getTaskAvailWokers(true);
//            $model->clearValidators();
//            $model->_validWorkers = array_keys($model->getTaskAvailWokers(true));
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
*/
        if ($model->load(Yii::$app->request->post()) ) {
            $model->getTaskAvailWokers(true);
            if ($model->save()) {
                $model->uploadFiles($this->getBehavior('validateFiles')->getData());
                return $this->redirect(['index']);
            }
        }


//            return $this->redirect(['view', 'id' => $model->task_id]);
//        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
//        }
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
            $model->uploadFiles($this->getBehavior('validateFiles')->getData());
            return $this->redirect(['index']);
//            return $this->redirect(['view', 'id' => $model->task_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Validate models
     * @param integer $id
     * @return mixed
     */
    public function actionValidatetask($id = 0)
    {
        if( $id == 0 ) {
            $model = new Tasklist();
        }
        else {
            $model = $this->findModel($id); // ->delete();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = $this->getBehavior('validateFiles')->validateData();

        $model->load(Yii::$app->request->post());
        $aModelResult = ActiveForm::validate($model);
//        Yii::info('result = ' . print_r($result, true));
//        Yii::info('aModelResult = ' . print_r($aModelResult, true));
        return array_merge($result, $aModelResult);
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
     * Deletes an existing Tasklist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUndelete($id)
    {
        $model = $this->findModel($id); // ->delete();
        if( $model !== null ) {
            $model->task_active = Tasklist::STATUS_ACTIVE;
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
/*        $aData = Yii::$app
            ->db
            ->createCommand('SELECT Distinct task_direct FROM tlst_tasklist Where task_dep_id = :depid And LENGTH(task_direct) > 0 Order By task_id Desc', ['depid'=>Yii::$app->request->get('depid', 0)])
            ->queryColumn();
*/
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $a = Subject::getList();
        foreach($a As $k=>$v) {
            $a[$k] = wordwrap($v, 120, "<br />\n");
        }
//            wordwrap($text, 20, "<br />\n");
//        $response->data = $aData;
        $response->data = $a;
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
     * Удаление всех переносов дат у задачи
     */
    public function actionDeltimeshift($id = 0)
    {
        if( Yii::$app->request->isAjax ) {
            $model = $this->findModel($id);
            $form = new AskForm();
            $form->text = 'Удалить переносы задачи ' . $model->getTasknum() . ' ?';
            $form->buttons[0]['exec'] = function() use ($model) {
                $model->task_numchanges = 0;
                foreach($model->changes As $ob) {
                    $ob->delete();
                }
                $model->task_reasonchanges = '';
                $model->task_finaltime = preg_match('|([\\d]+).([\\d]+).([\\d]+)|', $model->task_actualtime, $a) ? date('Y-m-d H:i:s', mktime(23, 59, 59, $a[2], $a[1], $a[3])) : $model->task_actualtime;
                $s = '';
                if( !$model->save() ) {
                    foreach($model->getErrors() As $a) {
                        $s .= implode(', ', $a);
                    }
                }
                return $s;
            };
            $form->buttons[0]['js'] = 'window.location.reload();';
            if( $form->load(Yii::$app->request->post()) ) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $form->runCommand();
            }
            else {
                return $this->renderAjax(
                    '/default/askform',
                    [
                        'model' => $form,
                    ]
                );
            }


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
//                'task_active' => Tasklist::STATUS_ACTIVE,
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
