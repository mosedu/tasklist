<?php

namespace app\modules\user\controllers;

use app\modules\task\models\Action;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;

use app\modules\user\models\LoginForm;
use app\modules\user\models\User;
use app\modules\user\models\UserSearch;
use app\modules\user\models\PasswordResetRequestForm;
use app\modules\user\models\ResetPasswordForm;
use app\modules\user\models\DateIntervalForm;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'create', 'view', 'update', 'delete', 'index', 'unlink', 'unlinkall', 'getkpi', ],
                'rules' => [
                    [
                        'actions' => ['logout', 'getkpi', ],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['create', 'update', 'view', 'delete', 'index', 'changerole', ],
                        'allow' => true,
                        'roles' => ['createUser'],
                    ],
                    [
                        'actions' => ['unlink', 'unlinkall', ],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     *
     * @return string|\yii\web\Response
     */
    public function actionGetkpi()
    {
        $model = new DateIntervalForm();
        $model->setdefaultValues();
        $aData = [];
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            return $this->render('kpi', [
                'model' => $model,
                'data' => $model->calcKpi(),
            ]);
        } else {
            return $this->render('kpi', [
                'model' => $model,
                'data' => $aData,
            ]);
        }
    }

    /**
     *
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     *
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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

        if ($model->load(Yii::$app->request->post())) {
            if( $model->save() ) {
                return $this->redirect(['index']);
//                return $this->redirect(['view', 'id' => $model->us_id]);
            }
            else {
                Yii::info("On save: " . print_r($model->getErrors(), true));
            }
        }
        else {
            Yii::info("On load: " . print_r($model->getErrors(), true));
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->us_login = preg_replace('/\\W/', '', $model->us_email);

            if( $model->save() ) {
//                return $this->redirect(['view', 'id' => $model->us_id]);
                return $this->redirect(['index', ]);
            }
            else {
                Yii::info("On update save: " . print_r($model->getErrors(), true));
            }
        }
        else {
            Yii::info("On update load: " . print_r($model->getErrors(), true));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
/*
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
*/
    }

    /**
     * Updates an existing User role
     * @param integer $id
     * @return mixed
     */
    public function actionChangerole($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->us_login = preg_replace('/\\W/', '', $model->us_email);

            if( $model->save() ) {
                return $this->redirect(['index', ]);
            }
            else {
                Yii::info("On update save: " . print_r($model->getErrors(), true));
            }
        }
        else {
            Yii::info("On update load: " . print_r($model->getErrors(), true));
        }

        return $this->render('changerole', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $aWhere = [
            'us_id' => $id,
        ];
        if( !Yii::$app->user->can('admin') ) {
            $aWhere['us_active'] = User::STATUS_ACTIVE;
        }

        $model = User::find()
            ->where($aWhere)
            ->with('department')
            ->one();
        if ( $model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id); // ->delete();
        if( $model !== null ) {
            $model->us_active = ($model->us_active == User::STATUS_DELETED) ? User::STATUS_ACTIVE : User::STATUS_DELETED;
            if( !$model->save() ) {
                Yii::error('Error delete ( save ) User: ' . print_r($model->getErrors(), true));
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing User model and all his DATA.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUnlink($id)
    {

        $model = $this->findModel($id);
        if( $model !== null ) {
            $auth = Yii::$app->authManager;
            $auth->revokeAll($id);
            $sSql = 'Delete From ' . Action::tableName() . ' Where act_us_id = ' . $id;
            $nDel = Yii::$app->db->createCommand($sSql)->execute();
            Yii::warning('actionUnlink('.$id.'): delete ' . $nDel . ' actions');
            $model->delete();
        }

        return $this->redirect(['index']);
    }

    /**
     * Deletes all User model and all his DATA.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionUnlinkall()
    {

        $aUsers = User::find()->all();
        $auth = Yii::$app->authManager;
        foreach($aUsers As $model) {
            if( $model->us_id < 2 ) {
                continue;
            }
            $auth->revokeAll($model->us_id);
            $sSql = 'Delete From ' . Action::tableName() . ' Where act_us_id = ' . $model->us_id;
            $nDel = Yii::$app->db->createCommand($sSql)->execute();
            Yii::warning('actionUnlinkall('.$model->us_id.'): delete ' . $nDel . ' actions');
            $model->delete();
        }

        return $this->redirect(['index']);
    }

    /*
     * Форма сброса пароля
     *
     */
    public function actionRequestpasswordreset()
    {
        $model = new PasswordResetRequestForm();
        if ( $model->load(Yii::$app->request->post()) ) {
//            $this->DoDelay('restoreform.delay.time');

            if( $model->validate() ) {
                if ($model->sendEmail()) {
                    Yii::$app->getSession()->setFlash('success', 'Спасибо! На ваш Email было отправлено письмо со ссылкой на восстановление пароля.');
                    return $this->goHome();
                } else {
                    Yii::$app->getSession()->setFlash('error', 'Извините. У нас возникли проблемы с отправкой письма восстановления пароля. Повторите попытку попозже.');
                }
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /*
     * Сброс пароля
     *
     * @param string $token
     *
     *
     */
    public function actionResetpassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage(), null, $e);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'Ваш пароль успешно изменен');
            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

}
