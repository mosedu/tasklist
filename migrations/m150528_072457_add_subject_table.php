<?php

use yii\db\Schema;
use app\components\MyMigration;

class m150528_072457_add_subject_table extends MyMigration
{
    public function up()
    {
        $tableOptionsMyISAM = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';
        }

        $this->createTable('{{%subject}}', [
            'subj_id' => Schema::TYPE_PK,
            'subj_title' => Schema::TYPE_TEXT . ' NOT NULL DEFAULT \'\'',
            'subj_created' => Schema::TYPE_DATETIME,
            'subj_dep_id' => Schema::TYPE_INTEGER . ' NULL',
            'subj_comment' => Schema::TYPE_STRING,
            'subj_is_active' => Schema::TYPE_INTEGER . ' DEFAULT 1',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_subj_is_active', '{{%subject}}', 'subj_is_active');
        $this->createIndex('idx_subj_title', '{{%subject}}', 'subj_title (32)');


        $this->refreshCache();

    }

    public function down()
    {
        $this->dropTable('{{%subject}}');
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
