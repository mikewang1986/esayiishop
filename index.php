<?php
// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
//加入gii
define('YII_ENV_DEV',true);
require(__DIR__ . '/app/components/helper.php');
require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');
$config = require(__DIR__ . '/app/config/web.php');
(new yii\web\Application($config))->run();
