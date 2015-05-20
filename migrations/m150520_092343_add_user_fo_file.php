<?php

use yii\db\Schema;
use yii\db\Migration;

class m150520_092343_add_user_fo_file extends Migration
{
    public function up()
    {
        $this->addColumn('{{%file}}', 'file_user_id', Schema::TYPE_INTEGER);
        $this->addColumn('{{%file}}', 'file_group', Schema::TYPE_INTEGER);
        $this->refreshCache();
    }

    public function down()
    {
        $this->dropColumn('{{%file}}', 'file_user_id');
        $this->dropColumn('{{%file}}', 'file_group');
        $this->refreshCache();
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
