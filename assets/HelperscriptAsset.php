<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 16.03.2015
 * Time: 13:31
 */

namespace app\assets;

use yii\web\AssetBundle;


class HelperscriptAsset extends AssetBundle {
    public $sourcePath = '@app/static';
    public $css = [];

    public $js = [
        'js/helper.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];

}