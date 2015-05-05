<?php

namespace app\modules\user\controllers;

use Yii;
use app\modules\user\models\User;
use app\modules\user\models\Department;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\user\models\ImportXlsForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class ImportController extends Controller
{
    public function behaviors()
    {
        return [];
    }

    /**
     * Import User.
     * @return mixed
     */
    public function actionXls()
    {
        $model = new ImportXlsForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $aUserData = $model->getFileData();
            $sDirectorDepartment = 'Администрация';
            $nAdmDepId = Department::getDepartmentIdByName($sDirectorDepartment);
            $aErr = [];
            foreach($aUserData As $ob) {
                $oUser = new User();
                $oUser->setFioByString($ob['fullname']);
                if( $ob['department'] == '' ) {
                    $depid = $nAdmDepId;
                }
                else {
                    $depid = Department::getDepartmentIdByName($ob['department']);
                }
                $oUser->us_dep_id = $depid;
                $oUser->us_email = $ob['email'];
                $oUser->us_workposition = $ob['workposition'];
                $oUser->newPassword = '111111';
                if( !$oUser->save() ) {
                    $aErr[] = implode(', ', $ob) . ' : ' . print_r($oUser->getErrors(), true);
                }
            }
            return $this->renderContent(
                str_replace(
                    "\n",
                    "<br />\n",
                    print_r($aErr, true)
                )
            );
        }

        return $this->render('import_xls', [
            'model' => $model,
        ]);

    }

    /**
     *
     * @return mixed
     */
    public function actionMasssend()
    {
        $a = User::find()->where(['>',  'us_createtime', '2015-05-01'])->all();
        foreach($a As $ob) {
            $ob->sendNotificate('user_change_link', 'Правильный адрес входа в Систему учета задач', []);
        }
    }

    /**
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect('/');
    }

    /**
     *
     * @return mixed
     */
    public function actionLogin()
    {
        return $this->redirect('/login');
    }

}
