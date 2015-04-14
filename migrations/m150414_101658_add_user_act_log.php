<?php

use yii\db\Schema;
use yii\db\Migration;

class m150414_101658_add_user_act_log extends Migration
{
    public function up()
    {
        $this->createIndex('idx_task_department', '{{%tasklist}}', 'task_dep_id');
        $this->createIndex('idx_task_finaltime', '{{%tasklist}}', 'task_finaltime');
        $this->createIndex('idx_task_active', '{{%tasklist}}', 'task_active');

        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%action}}', [
            'act_id' => Schema::TYPE_PK,
            'act_us_id' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'act_type' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'act_createtime' => Schema::TYPE_DATETIME . ' NOT NULL',
            'act_data' => Schema::TYPE_TEXT . ' DEFAULT \'\'',
            'act_table' => Schema::TYPE_STRING . ' DEFAULT \'\'',
            'act_table_pk' => Schema::TYPE_BIGINT,
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_act_table_pk', '{{%action}}', 'act_table_pk');
        $this->createIndex('idx_act_table', '{{%action}}', 'act_table');
        $this->createIndex('idx_act_type', '{{%action}}', 'act_type');
        $this->createIndex('idx_act_createtime', '{{%action}}', 'act_createtime');
        $this->createIndex('idx_act_us_id', '{{%action}}', 'act_us_id');
        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%action}}');

        $this->dropIndex('idx_task_department', '{{%tasklist}}');
        $this->dropIndex('idx_task_finaltime', '{{%tasklist}}');
        $this->dropIndex('idx_task_active', '{{%tasklist}}');

        $this->refreshCache();
        return true;
    }

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
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
