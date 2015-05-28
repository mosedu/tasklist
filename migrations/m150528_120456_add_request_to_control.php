<?php

use yii\db\Schema;
use app\components\MyMigration;

class m150528_120456_add_request_to_control extends MyMigration
{
    public function up()
    {
        $tableOptionsMyISAM = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        }

        $this->createTable('{{%requestmsg}}', [
            'req_id' => Schema::TYPE_PK,
            'req_user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'req_task_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'req_text' => Schema::TYPE_STRING,
            'req_comment' => Schema::TYPE_STRING,
            'req_created' => Schema::TYPE_DATETIME,
            'req_data' => Schema::TYPE_TEXT,
            'req_is_active' => Schema::TYPE_INTEGER . ' DEFAULT 1',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_req_is_active', '{{%requestmsg}}', 'req_is_active');
        $this->createIndex('idx_req_task_id', '{{%requestmsg}}', 'req_task_id');
        $this->createIndex('idx_req_user_id', '{{%requestmsg}}', 'req_user_id');

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%requestmsg}}');
        $this->refreshCache();
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
