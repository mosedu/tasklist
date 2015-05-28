<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 28.05.2015
 * Time: 9:42
 */

namespace app\components;

use yii;
use yii\db\Schema;
use yii\db\Migration;


class MyMigration extends Migration {

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
    }
}


