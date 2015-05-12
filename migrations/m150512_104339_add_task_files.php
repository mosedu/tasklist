<?php

use yii\db\Schema;
use yii\db\Migration;

class m150512_104339_add_task_files extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        }

        $this->createTable('{{%file}}', [
            'file_id' => Schema::TYPE_PK,
            'file_time' => Schema::TYPE_DATETIME,
            'file_orig_name' => Schema::TYPE_STRING . ' NOT NULL',
            'file_task_id' => Schema::TYPE_INTEGER,
            'file_size' => Schema::TYPE_INTEGER . ' NOT NULL',
            'file_type' => Schema::TYPE_STRING,
            'file_name' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_file_task_id', '{{%file}}', 'file_task_id');
        $this->createIndex('idx_file_name', '{{%file}}', 'file_name');
    }

    public function down()
    {
        $this->dropTable('{{%file}}');

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
