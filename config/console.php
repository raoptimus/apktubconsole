<?php
Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => array_merge([
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@runtime/cache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'letyii\rbacmongodb\MongodbManager',
            'itemTable' => 'AuthItems',
            'itemChildTable' => 'AuthItemChildren',
            'assignmentTable' => 'AuthAssignments',
            'ruleTable' => 'AuthRules'
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'htmlLayout' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'mx....apk....',
                'username' => '...apk@...apk....',
                'password' => 'KmenFesstd',
                'port' => '25',
            ],
        ],
    ], require(__DIR__ . "/db.php")),
    'params' =>  require(__DIR__ . '/params.php'),
];