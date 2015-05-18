<?php

use yii\db\Schema;
use yii\db\Migration;

class m150518_082133_add_multiworker_in_task extends Migration
{
    public function up()
    {

        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%worker}}', [
            'worker_id' => Schema::TYPE_PK,
            'worker_task_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'worker_us_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_worker_task_id', '{{%worker}}', 'worker_task_id');
        $this->createIndex('idx_worker_us_id', '{{%worker}}', 'worker_us_id');

        $sSql = 'Insert Into {{%worker}} (worker_task_id, worker_us_id) Select task_id As worker_task_id, task_worker_id As worker_us_id FROM {{%tasklist}} Where task_worker_id > 0';

        $nRec = Yii::$app->db->createCommand($sSql)->execute();

        echo "Inserted {$nRec} records\n\n";

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%worker}}');
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
