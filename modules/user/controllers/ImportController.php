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
     * Lists all User models.
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

}
