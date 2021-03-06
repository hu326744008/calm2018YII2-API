<?php
return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'timezone' => 'PRC',
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['error'],
                    'logFile' => '@app/runtime/logs/error.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['warn'],
                    'logFile' => '@app/runtime/logs/warn.log',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['info'],
                    'maxFileSize'=>102400,//单个日志文件大小100M
                    'maxLogFiles'=>10,//日志文件最大几份
                    'logFile' => '@app/runtime/logs/info'.date('Ymd').'.log',
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager'=>[
            'showScriptName'=>false,//注意false不要用引号括上 是否显示index.php 搭配.htaccess
            'enablePrettyUrl' => true,//开启路由
            //'suffix' => '.html',//后缀
            'rules' =>[
            // 'sites'=>'/controllers/admin/index',
            
        ],],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=127.0.0.1;dbname=cndata',
            'username' => 'root',
            'password' => '123456',
            'charset' => 'utf8',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'password'=>'isanjie123',
            'database' => 0,
        ],
    ],
    'aliases' => [
        '@Api' => '@app/libs/api'
    ],
    'params' => [   
        'adminEmail' => 'admin@example.com',
        'java_api_url' =>'http://192.168.100.185:7080/',
        'java_api_url1' =>'http://192.168.100.185:7080/',
        'rcodes' => 'http://192.168.100.185:7080/',
        'returnpay' => 'http://192.168.100.185:8011/web/payreturn/callback',
        'pc_style'=>"/resources/",
        'pic_host' => 'http://futureshop.oss-cn-qingdao.aliyuncs.com/'],
];
