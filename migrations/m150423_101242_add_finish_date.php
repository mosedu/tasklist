<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\task\models\Tasklist;

class m150423_101242_add_finish_date extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tasklist}}', 'task_finishtime', Schema::TYPE_DATETIME);
        $this->refreshCache();
        $sSql = 'Update {{%tasklist}} Set task_finishtime = task_actualtime Where task_progress = ' . Tasklist::PROGRESS_FINISH;
        $nUpdate = Yii::$app->db->createCommand($sSql)->execute();
        echo "Updated: {$nUpdate}\n";
    }

    public function down()
    {
        $this->dropColumn('{{%tasklist}}', 'task_finishtime');
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
