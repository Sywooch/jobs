<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'rest-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'apns' => [
            'class' => 'bryglen\apnsgcm\Apns',
            'environment' => \bryglen\apnsgcm\Apns::ENVIRONMENT_SANDBOX,
            'pemFile' => dirname(__FILE__).'/apnssert/production.pem',
            // 'retryTimes' => 3,
            'options' => [
                'sendRetryTimes' => 5
            ]
        ],
        'fcm' => [
            'class' => 'understeam\fcm\Client',
            'apiKey' => 'AAAAwshBTgc:APA91bEug0iTVwr2n-oYhsTbbcJnOVim8Od6lI9320uxJcMHwdKboGGrqgcd6ZfRn0uH4a9Uf7YeOlxTQ5WYlV3STIKmkmYSCHIpmec0_5Bk-aZ5_qeD7ClGuxSJazgKrfv1RmC6LN7-',
        ],
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
                'api/user-data' => '/api/user/get-user-data',
                'api/push-notification' => '/api/user/push-notification',
                'api/profile' => '/api/profile/profile',
                'api/change-profile' => '/api/profile/change-profile',
                'api/change-password' => '/api/profile/change-password',
                'api/avatar-upload' => '/api/user/avatar-upload',
                'api/category' => '/api/post/category',
                'api/post-create' => '/api/post/create',
                'api/post-update' => '/api/post/update',
                'api/post-delete' => '/api/post/delete',
                'api/post-image' => '/api/post/upload-post-image',
                'api/post-image-delete' => '/api/post/delete-post-image',
                'api/post-search' => '/api/post/post-search',
                'api/user-posts' => '/api/post/user-posts',
                'api/posts-by-category' => '/api/post/posts-by-category',
                'api/get-post' => '/api/post/get-post',
                'api/get-post-images' => '/api/post/get-post-images',
                'api/add-favorite' => '/api/favorite/add-favorite',
                'api/favorites' => '/api/favorite/favorites',
                'api/remove-favorite' => '/api/favorite/remove-favorite',
                'api/send-message' => '/api/message/send-message',
                'api/get-message' => '/api/message/get-message',
                'api/upload-message-photo' => '/api/message/upload-message-photo',
                'api/inbox-users' => '/api/message/inbox-users',
                'api/outbox-users' => '/api/message/outbox-users',
                'api/story' => '/api/message/story',
                'api/search-message' => '/api/message/search-message',
                'api/search-user' => '/api/message/user-search',
                'api/delete-inbox-message' => '/api/message/delete-message-by-sender-id',
                'api/test-geo' => '/api/message/test-geo'
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
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@app/mail',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'crm.urich@gmail.com',
                'password' => '1995202009vasya',
                'port' => '587',
                'encryption' => 'tls',
            ],
            'useFileTransport' => false,
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
