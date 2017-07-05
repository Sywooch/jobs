<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'rest-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
//    'modules' => [
//        'api' => [
//            'class' => 'app\modules\api\ApiModule',
//        ]
//    ],
    'components' => [
        'request' => [
            'baseUrl'=> '',
            'enableCookieValidation' => false,
            'enableCsrfCookie' => false,
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'on beforeSend' => function ($event) {
                header("Access-Control-Allow-Origin: *");
            }
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['api/user'],
                    'pluralize'=>false
                ],
                'api' => '/api/user/index',
                'api/test' => '/api/user/test',
                'api/logout' => '/api/user/logout',
                'api/login' => '/api/user/login',
                'api/glogin' => '/api/user/glogin',
                'api/flogin' => '/api/user/flogin',
                'api/llogin' => '/api/user/llogin',
                'api/profile' => '/api/profile/profile',
                'api/change-profile' => '/api/profile/change-profile',
                'api/avatar-upload' => '/api/user/avatar-upload'
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
            'loginUrl' => null
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
