<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2019-01-06 12:47
 */

namespace backend\assets;

/**
 * 重要提示：启用配置后，修改此处的js/css将不会生效
 * 需要在backend/config/main.php中assetManager.bundles处修改配置
 *
 * Class WebuploaderAsset
 * @package backend\assets
 */
class WebuploaderAsset extends \yii\web\AssetBundle
{
    public $css = [
    	'css/plugins/webuploader/style.css',
        'css/plugins/webuploader/webuploader.css',
    ];
    public $js = [
        'js/plugins/webuploader/webuploader.min.js',
        'js/plugins/webuploader/init.js'
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $basePath = "@web";

    public $sourcePath = '@backend/web/static/';
}
