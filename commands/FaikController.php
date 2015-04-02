<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

use app\modules\user\models\User;
use app\modules\user\models\Department;
use yii\db\Expression;


class FaikController extends Controller
{
    public function actionCreate()
    {
        /** @var \yii\rbac\DbManager $auth */
        $auth = Yii::$app->authManager;
        $aRoles = [User::ROLE_CONTROL, User::ROLE_DEPARTMENT];
        $nDep = 10;
        $aTitle = [
            'Плановый',
            'Бухгалтерский',
            'Поддержки',
            'Развития',
            'Снабжения',
            'Технологии',
            'Продвижения',
            'Консультаций',
            'Повышения квалификации',
            'Транспортный',
        ];

        $aDep = [];
        $nCou = count($aTitle);
        echo "count = {$nCou}\n";
        $bNoControl = true;
        for($i = 0; $i <$nDep; $i++) {
            $ob = new Department();
            $n1 = floor(mt_rand(0, $nCou - 1));
            echo "n1 = $n1\n";
            $s = $aTitle[$n1] . ($i ? (' ' . $i) : '');
            $ob->attributes = [
                'dep_name' => 'Отдел ' . $s,
                'dep_shortname' => $s,
                'dep_user_roles' => $aRoles[(mt_rand(0, 10) > 8) && $bNoControl ? 0 : 1],
                'dep_active' => User::STATUS_ACTIVE,
            ];
            if( $ob->save() ) {
                if( $ob->dep_user_roles === User::ROLE_CONTROL ) {
                    $bNoControl = false;
                }
                $aDep[] = $ob->attributes;
            }
            else {
                echo print_r($ob->getErrors(), true);
            }
        }

        if( count($aDep) == 0 ) {
            return;
        }

        $sFam = 'Смирнов Иванов Кузнецов Попов Соколов Лебедев Козлов Новиков Морозов Петров Волков Соловьев Васильев Зайцев Павлов Семенов Голубев Виноградов Богданов Воробьев Федоров Михайлов Беляев Тарасов Белов Комаров Орлов Киселев Макаров Андреев Ковалев Ильин Гусев Титов Кузьмин Кудрявцев Баранов Куликов Алексеев Степанов';
        $sName = 'Максим Иван Артем Дмитрий Никита Михаил Даниил Егор Андрей';
        $sOtch = 'Александрович Сергеевич Владимирович Николаевич Алексеевич Викторович Андреевич Юрьевич Михайлович Дмитриевич Анатольевич Евгеньевич Игоревич Иванович Валерьевич Вячеславович Васильевич Олегович Павлович Борисович Геннадьевич';
        $aFam = explode(' ', $sFam);
        $aName = explode(' ', $sName);
        $aOtch = explode(' ', $sOtch);

        $nPup = 40;
        $oRoles = [];
        for($i = 0; $i <$nPup; $i++) {
            $ob = new User();
            $nDep = floor(mt_rand(0, count($aDep)-1));
            $nRole = $aDep[$nDep]['dep_user_roles'];
            if( !isset($oRoles[$nRole]) ) {
                $oRoles[$nRole] = Yii::$app->authManager->getRole($nRole);
            }
            $ob->attributes = [
                'us_active' => User::STATUS_ACTIVE,
                'us_dep_id' => $aDep[$nDep]['dep_id'],
                'us_email' => sprintf("t%03d@mail.ru", $i),
                'us_name' => $aName[floor(mt_rand(0, count($aName)-1))],
                'us_secondname' => $aOtch[floor(mt_rand(0, count($aOtch)-1))],
                'us_lastname' => $aFam[floor(mt_rand(0, count($aFam)-1))],
                'us_login' => sprintf("t%03d", $i),
                'us_createtime' => new Expression('NOW()'),
                'us_workposition' => 'Profession ' . $i,
                'us_role_name' => $oRoles[$nRole]->name,
            ];
            $ob->setPassword('1111');
            $ob->us_auth_key;

            if( $ob->save() ) {
                echo "User {$i}: {$ob->us_id} -> ".$oRoles[$nRole]->name."\n";
//                $auth->assign($oRoles[$nRole], $ob->us_id);
            }
            else {
                echo print_r($ob->getErrors(), true);
            }
        }
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
