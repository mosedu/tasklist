<?php

use yii\db\Schema;
use yii\db\Migration;

use app\modules\task\models\Tasklist;

class m150416_131503_add_reason_table extends Migration
{
    public function up()
    {

        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%changes}}', [
            'ch_id' => Schema::TYPE_PK,
            'ch_us_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'ch_task_id' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            'ch_data' => Schema::TYPE_STRING . ' DEFAULT \'\'',
            'ch_text' => Schema::TYPE_TEXT . ' DEFAULT \'\'',
        ], $tableOptionsMyISAM);

/*
        $sSql = 'Select * From ' . Tasklist::tableName() . ' Where task_finaltime <> task_actualtime';
        $reader = Yii::$app->db->createCommand($sSql)->query();
        while ($row = $reader->read()) {
            $sSql = 'Insert Into {{%changes}} (ch_us_id, ch_task_id, ch_data, ch_text) Values (:uid, :tid, :change, :descr)';
            Yii::$app->db->createCommand(
                $sSql,
                [
                    ':uid' => $row[''],
                    ':tid',
                    ':change',
                    ':descr'
                ]
            )->execute();
        }
*/
        $this->createIndex('idx_change_user', '{{%changes}}', 'ch_us_id');
        $this->createIndex('idx_change_task', '{{%changes}}', 'ch_task_id');

        $this->refreshCache();
    }

    public function down()
    {

        $this->dropIndex('idx_change_user', '{{%changes}}');
        $this->dropIndex('idx_change_task', '{{%changes}}');

        $this->dropTable('{{%changes}}');
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
