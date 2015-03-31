<?php

use yii\db\Schema;
use yii\db\Migration;

class m150331_135032_add_user_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%user}}', [
            'us_id' => Schema::TYPE_PK,
            'us_active' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'us_email' => Schema::TYPE_STRING . ' NOT NULL',
            'us_password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'us_name' => Schema::TYPE_STRING . ' NOT NULL',
            'us_secondname' => Schema::TYPE_STRING,
            'us_lastname' => Schema::TYPE_STRING,
            'us_login' => Schema::TYPE_STRING,
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
    }

    public function down()
    {
        $this->dropTable('{{%user}}');

        return true;
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
