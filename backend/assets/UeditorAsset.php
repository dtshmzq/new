<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2017-03-15 21:16
 */

namespace backend\assets;


/**
 * 重要提示：启用配置后，修改此处的js/css将不会生效
 * 需要在backend/config/main.php中assetManager.bundles处修改配置
 * Class UeditorAsset
 * @package backend\assets
 */
class UeditorAsset extends \yii\web\AssetBundle
{

    public $basePath = "@web";

    public $sourcePath = '@backend/web/static/js/plugins/ueditor/';

    public $js = [
        'ueditor.all.js',
    ];

    public $publishOptions = [
        'except' => [
            'php/',
            'index.html',
            '.gitignore'
        ]
    ];

}
