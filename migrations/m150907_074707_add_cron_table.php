<?php

use yii\db\Schema;
use yii\db\Migration;

class m150907_074707_add_cron_table extends Migration
{
    public function up()
    {

        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%crontab}}', [
            'cron_id' => Schema::TYPE_PK,
            'cron_min' => Schema::TYPE_STRING . '(96) Not Null DEFAULT \'*\' Comment \'Минуты\'',
            'cron_hour' => Schema::TYPE_STRING . '(96) Not Null DEFAULT \'*\' Comment \'Часы\'',
            'cron_day' => Schema::TYPE_STRING . '(96) Not Null DEFAULT \'*\' Comment \'Дни месяца\'',
            'cron_wday' => Schema::TYPE_STRING . '(96) Not Null DEFAULT \'*\' Comment \'Дни недели\'',
            'cron_path' => Schema::TYPE_STRING . '(128) Not Null DEFAULT \'\' Comment \'Путь\'',
            'cron_tstart' => Schema::TYPE_DATETIME . ' Comment \'Начато\'',
            'cron_tlast' => Schema::TYPE_DATETIME . ' Comment \'Окончено\'',
            'cron_comment' => Schema::TYPE_STRING . '(128) Not Null DEFAULT \'\' Comment \'Комментарий\'',
            'cron_isactive' => Schema::TYPE_SMALLINT . ' Not Null DEFAULT 1 Comment \'Активно\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_cron_isactive', '{{%crontab}}', 'cron_isactive');
//        $this->createIndex('idx_cron_tlast', '{{%crontab}}', 'cron_tlast');
        $this->createIndex('idx_cron_tstart', '{{%crontab}}', 'cron_tstart');

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%crontab}}');
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
