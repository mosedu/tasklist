<?php

use yii\db\Schema;
use yii\db\Migration;
use yii\db\Expression;
use yii\base\InvalidConfigException;
use yii\rbac\DbManager;
use app\modules\user\models\User;

/*************************************************************
 *                    RUN BEFORE THIS:                       *
 *                                                           *
 *     yii migrate --migrationPath=@yii/rbac/migrations/     *
 *                                                           *
 *************************************************************/
class m150331_135032_add_user_table extends Migration
{
    public function up()
    {
        $authManager = $this->getAuthManager();
        $sSql = 'Describe ' . $authManager->ruleTable;
        try {
            $this->db->createCommand($sSql)->query();
        }
        catch(yii\db\Exception $e) {
            $n = 60;
            $s1 = str_repeat('*', $n + 2) . "\n";
            $s2 = '*' . str_pad('', $n, " ", STR_PAD_BOTH) . "*\n";
            echo "\n" . $s1 . $s2;
            echo '*' . str_pad("RUN RBAC MIGRATION FIRST !", $n, " ", STR_PAD_BOTH) . "*\n";
            echo $s2;
            echo '*' . str_pad("php yii migrate --migrationPath=@yii/rbac/migrations/", $n, " ", STR_PAD_BOTH) . "*\n";
            echo $s2 . $s1 . "\n";
            echo '*' . str_pad("php yii rbac/create", $n, " ", STR_PAD_BOTH) . "*\n";
            echo $s2 . $s1 . "\n";
            return false;
        }

        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%department}}', [
            'dep_id' => Schema::TYPE_PK,
            'dep_name' => Schema::TYPE_STRING . ' NOT NULL',
            'dep_shortname' => Schema::TYPE_STRING . '',
            'dep_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 1',
            'dep_user_roles' => Schema::TYPE_STRING,
        ], $tableOptionsMyISAM);

        $sSql = 'Insert Into {{%department}} (dep_name, dep_shortname, dep_active, dep_user_roles) Values (\'Отдел мониторинга и контроля\', \'Контроль\', 1, \''.User::ROLE_CONTROL.'\')';
        $nEl = $this->db->createCommand($sSql)->execute();
        echo "\n-------------------------------------\nInsert {$nEl} department\n-------------------------------------\n";

        $this->createTable('{{%user}}', [
            'us_id' => Schema::TYPE_PK,
            'us_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'us_dep_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'us_email' => Schema::TYPE_STRING . ' NOT NULL',
            'us_password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'us_name' => Schema::TYPE_STRING . ' NOT NULL',
            'us_secondname' => Schema::TYPE_STRING,
            'us_lastname' => Schema::TYPE_STRING,
            'us_login' => Schema::TYPE_STRING,
            'us_role_name' => Schema::TYPE_STRING,
            'us_logintime' => Schema::TYPE_DATETIME,
            'us_createtime' => Schema::TYPE_DATETIME . ' NOT NULL',
            'us_workposition' => Schema::TYPE_STRING,
            'us_auth_key' => Schema::TYPE_STRING . '(32) NULL DEFAULT NULL',
            'us_email_confirm_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
            'us_password_reset_token' => Schema::TYPE_STRING . ' NULL DEFAULT NULL',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_user_username', '{{%user}}', 'us_login');
        $this->createIndex('idx_user_email', '{{%user}}', 'us_email');
        $this->createIndex('idx_user_status', '{{%user}}', 'us_active');
        $this->createIndex('idx_user_dep', '{{%user}}', 'us_dep_id');

        $aFields = [
            'us_active' => 1,
            'us_email' => 'devmosedu@yandex.ru',
            'us_password_hash' => Yii::$app->security->generatePasswordHash('123456'),
            'us_name' => 'admin',
            'us_login' => 'admin',
            'us_auth_key' => Yii::$app->security->generateRandomString(),
            'us_role_name' => User::ROLE_ADMIN,
            'us_workposition' => 'admin',
            'us_createtime' => new Expression('NOW()'),
        ];

        $aParam = array_combine(
            array_map(function($el) { return ':' . $el; }, array_keys($aFields)),
            array_values($aFields)
        );
        $sSql = 'Insert Into {{%user}} ('
                . implode(',', array_keys($aFields))
                . ') Values ('
                . implode(',', array_keys($aParam))
                . ')';

        // $this->execute($sSql, $aParam);
        $nEl = $this->db->createCommand($sSql)->bindValues($aParam)->execute();
        echo "\n-------------------------------------\nInsert {$nEl} users\n-------------------------------------\n";
        $auth = Yii::$app->authManager;
        $role = $auth->getRole(User::ROLE_ADMIN);
        $auth->revokeAll(1);
        $auth->assign($role, 1);

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
        $this->dropTable('{{%department}}');

        $this->refreshCache();

        return true;
    }

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
    }

    /**
     * @throws yii\base\InvalidConfigException
     * @return DbManager
     */
    protected function getAuthManager()
    {
        $authManager = Yii::$app->getAuthManager();
        if (!$authManager instanceof DbManager) {
            throw new InvalidConfigException('You should configure "authManager" component to use database before executing this migration.');
        }
        return $authManager;
    }

    /*
   // Use safeUp/safeDown to run migration code within a transaction
   public function safeUp()
   {
   }

   public function safeDown()
   {
   }
   */
}
