<?php

use yii\web\User as WebUser;

$config = [
    'id' => 'app',
    'defaultRoute' => 'main/default/index',
    'language' => 'ru',
    'components' => [
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => null,
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'css' => ['css/custom-bootstrap.css'],
                ],
            ],
        ],

        'request' => [
            'cookieValidationKey' => 'MRUy44nr8pp124k94uZaxH0JI9KD92w-',
        ],
        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/default/login'],
//            'on ' . WebUser::EVENT_AFTER_LOGIN => ['app\modules\user\models\User', 'afterLogin']
        ],
        'errorHandler' => [
            'errorAction' => 'main/default/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
