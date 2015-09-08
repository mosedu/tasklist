<?php

use yii\db\Schema;
use yii\db\Migration;

class m150908_102619_add_operate_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%taskflag}}', [
            'tf_id' => Schema::TYPE_PK,
            'tf_flag' => Schema::TYPE_SMALLINT . ' Not Null DEFAULT 0 Comment \'Флаг\'',
            'tf_task_id' => Schema::TYPE_INTEGER . ' Not Null DEFAULT 0 Comment \'Задача\'',
            'tf_date' => Schema::TYPE_DATETIME . ' Comment \'Установлен\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_tf_task_id', '{{%taskflag}}', 'tf_task_id');
        $this->createIndex('idx_tf_flag', '{{%taskflag}}', 'tf_flag');

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%taskflag}}');
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
