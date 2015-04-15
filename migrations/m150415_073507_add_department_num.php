<?php

use yii\db\Schema;
use yii\db\Migration;

class m150415_073507_add_department_num extends Migration
{
    public function up()
    {
        $this->addColumn('{{%department}}', 'dep_num', Schema::TYPE_INTEGER);
        $command = Yii::$app->db->createCommand('Update {{%department}} Set dep_num = dep_id Where dep_id > 0');
        $nUpd = $command->execute();
        echo "Updated: {$nUpd} records\n";
        $this->createIndex('idx_dep_num', '{{%department}}', 'dep_num');


        $this->refreshCache();
    }

    public function down()
    {
        $this->dropIndex('idx_dep_num', '{{%department}}');
        $this->dropColumn('{{%department}}', 'dep_num');
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
