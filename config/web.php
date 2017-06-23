<?php


$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'dashboard',
    'language' => 'ru-RU',
    'components' => array_merge([
        'assetManager' => [
            'appendTimestamp' => true,
            'linkAssets' => true,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                        'dict' => 'dict.php',
                    ],
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'MmXBZtxr1qDM0nDR9QsvZekGbHGpIszD',
        ],
        'session' => [
            'class' => 'yii\mongodb\Session',
            'db' => 'mongodb',
            'sessionCollection' => 'sessions',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'authManager' => [
            'class' => 'letyii\rbacmongodb\MongodbManager',
            'db' => 'authDb',
            'itemTable' => 'AuthItems',
            'itemChildTable' => 'AuthItemChildren',
            'assignmentTable' => 'AuthAssignments',
            'ruleTable' => 'AuthRules'
        ],
        'urlManager' => [
            'class' => 'app\components\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<project>/ads-icon/get-icon/<id:\w+>' => '<project>/ads-icon/get-icon',
                '<project>/ads-screen-shot/get-shot/<id:\w+>' => '<project>/ads-screen-shot/get-shot',
                '<project>/avatar/get/<id:\w+>' => 'avatar/get',
                'avatar/get/<id:\w+>' => 'avatar/get',
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
            'fileTransportPath' => "@runtime/email",
            'htmlLayout' => false,
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
        'jsonrpc' => [
            'class' => '\app\components\tubeserver\v1\Connection',
            'hostname' => 'host.com',
            'port' => 1235,
        ],
        'videoManager' => [
            'class' => 'app\components\VideoManager',
        ],
        'formatter' => [
            'thousandSeparator' => ' ',
        ],

    ],
        require(__DIR__ . '/db.php')),
    'modules' => [
        /*        'gii'=>array(
                    'class'=>'system.gii.GiiModule',
                    'password'=>'123',
                    // 'ipFilters'=>array(...a list of IPs...),
                    // 'newFileMode'=>0666,
                    // 'newDirMode'=>0777,
                ),*/
        'redactor' => 'yii\redactor\RedactorModule',
    ],
    'params' => require(__DIR__ . '/params.php'),
    'controllerMap' => [
        'mongodb-migrate' => 'yii\mongodb\console\controllers\MigrateController'
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\\debug\\Module',
        'allowedIPs' => $config['params']['whiteList'],
        'panels' => [
            'mongodb' => [
                'class' => 'yii\\mongodb\\debug\\MongoDbPanel',
                'db' => 'mongodb',
            ],
        ]
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
