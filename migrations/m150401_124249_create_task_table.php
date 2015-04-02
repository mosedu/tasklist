<?php

use yii\db\Schema;
use yii\db\Migration;

class m150401_124249_create_task_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%tasklist}}', [
            'task_id' => Schema::TYPE_PK,
            'task_dep_id' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',
            'task_num' => Schema::TYPE_INTEGER,                                     // 1
            'task_direct' => Schema::TYPE_TEXT . ' Default \'\'',                   // 2
            'task_name' => Schema::TYPE_TEXT . ' NOT NULL',                         // 3
            'task_type' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',           // 4
            'task_createtime' => Schema::TYPE_DATETIME . ' NOT NULL',
            'task_finaltime' => Schema::TYPE_DATETIME . ' NOT NULL',                // 5
            'task_actualtime' => Schema::TYPE_DATETIME . ' NOT NULL',               // 6
            'task_numchanges' => Schema::TYPE_INTEGER . ' DEFAULT 0',              // 7
            'task_reasonchanges' => Schema::TYPE_TEXT . ' DEFAULT \'\'',               // 8
            'task_progress' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 0',       // 9
            'task_summary' => Schema::TYPE_TEXT . ' DEFAULT \'\'',                  // 10
        ], $tableOptionsMyISAM);

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%tasklist}}');

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
