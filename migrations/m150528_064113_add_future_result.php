<?php

use yii\db\Schema;
use app\components\MyMigration;

class m150528_064113_add_future_result extends MyMigration
{
    public function up()
    {
        $this->addColumn('{{%tasklist}}', 'task_expectation', Schema::TYPE_TEXT . ' DEFAULT \'\'');
        $this->refreshCache();

    }

    public function down()
    {
        $this->dropColumn('{{%tasklist}}', 'task_expectation');
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
