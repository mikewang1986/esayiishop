<?php
$params = require(__DIR__ . '/params.php');
$basePath =  dirname(__DIR__);
$webroot = dirname($basePath);
$config = [
    'id' => 'app',
    'basePath' => $basePath,
    'bootstrap' => ['log'],
   // 'language' => 'en-US',
    'language' => 'zh_CN',
    'runtimePath' => $webroot . '/runtime',
    'vendorPath' => $webroot . '/vendor',
    //加入自定义组件
    'aliases'=>[
       '@common_classes' => $webroot."/components",
   ],
    'components' => [

        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'test',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'urlManager' => [
            'rules' => [
                //add by wanglei 加入接口
                array('route'=>'apiwap/list', 'pattern' => 'apiwap/<model:\w+>', 'verb' => ['GET','OPTIONS'],'defaults'=>['<model>']),
                array('route'=>'apiwap/view', 'pattern' => 'apiwap/<model:\w+>/<id:\d+>', 'verb' => ['GET','OPTIONS'],'defaults'=>['<model>','<id>']),
                array('route'=>'apiwap/update', 'pattern' => 'apiwap/<model:\w+>/<id:\d+>', 'verb' => ['PUT','OPTIONS'],'defaults'=>['<model>','<id>']),
                array('route'=>'apiwap/delete', 'pattern' => 'apiwap/<model:\w+>/<id:\d+>', 'verb' => ['DELETE','OPTIONS'],'defaults'=>['<model>','<id>']),
                array('route'=>'apiwap/create', 'pattern' => 'apiwap/<model:\w+>', 'verb' =>['POST','OPTIONS'],'defaults'=>['<model>','<id>']),
                array('route'=>'apiwap/update', 'pattern' => 'apiwap/<model:\w+>/<type:\w+>/<id:\d+>', 'verb' => 'PUT,OPTIONS'),
                '<controller:\w+>/view/<slug:[\w-]+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/cat/<slug:[\w-]+>' => '<controller>/cat',
            ],
        ],
        //image扩展
       /* 'image' => array(
            'class' => 'app\extensions\image\CImageComponent',
            // GD or ImageMagick
            'driver' => 'GD',
        ),*/
        'assetManager' => [
            // uncomment the following line if you want to auto update your assets (unix hosting only)
            //'linkAssets' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [YII_DEBUG ? 'jquery.js' : 'jquery.min.js'],
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [YII_DEBUG ? 'css/bootstrap.css' : 'css/bootstrap.min.css'],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [YII_DEBUG ? 'js/bootstrap.js' : 'js/bootstrap.min.js'],
                ],
            ],
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

//gii配置
if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
    $config['components']['db']['enableSchemaCache'] = false;
}
return array_merge_recursive($config, require($webroot . '/vendor/noumo/easyii/config/easyii.php'));