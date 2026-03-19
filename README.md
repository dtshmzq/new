将
frontend/web/index.php
frontend/web/sfadm/index.php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
改为
require __DIR__ . '/../../../vendor/autoload.php';
require __DIR__ . '/../../../vendor/yiisoft/yii2/Yii.php';

将
common/config/main.php
'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
改为
'vendorPath' => dirname(dirname(__DIR__)) . '/../vendor',