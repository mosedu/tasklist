<?php

use yii\db\Schema;
// use yii\db\Migration;
use app\components\MyMigration;

class m150615_073924_add_log_type extends MyMigration
{
    public function up()
    {
        $this->addColumn('{{%action}}', 'act_parenttype', Schema::TYPE_STRING. ' DEFAULT \'\'');
        $this->createIndex('idx_act_parenttype', '{{%action}}', 'act_parenttype');
        $this->refreshCache();
        $sSql = 'Update {{%action}} Set act_parenttype = CONCAT(UPPER(SUBSTRING(SUBSTRING(REPLACE(act_table, \'}}\', \'\'), 4), 1, 1)), SUBSTRING(REPLACE(act_table, \'}}\', \'\'), 5)) Where act_id > 0';
        // SELECT CONCAT(UPPER(SUBSTRING(SUBSTRING(REPLACE(act_table, '}}', ''), 4), 1, 1)), SUBSTRING(REPLACE(act_table, '}}', ''), 5)) FROM tlst_action
        $nUpdate = $this->db->createCommand($sSql)->execute();
        echo "\n\nUpdate {$nUpdate} records\n\n";
    }

    public function down()
    {
        $this->dropIndex('idx_act_parenttype', '{{%action}}');
        $this->dropColumn('{{%action}}', 'act_parenttype');
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
