<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Url;
use yii\helpers\Html;
use common\widgets\JsBlock;
use frontend\assets\AppAsset;
use frontend\widgets\MenuView;
use frontend\widgets\FriendlyLinkView;

AppAsset::register($this);

$this->beginPage()
?>
<!doctype html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="<?= Yii::$app->charset ?>"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
<meta name="applicable-device" content="pc,mobile" />
<?= Html::csrfMetaTags() ?>
<title><?= Html::encode($this->title) ?></title>
<link rel="shortcut icon" href="/favicon.ico">
<?php
$this->head();
!isset($this->metaTags['keywords']) && $this->registerMetaTag(['name' => 'keywords', 'content' => Yii::$app->feiber->seo_keywords], 'keywords');
!isset($this->metaTags['description']) && $this->registerMetaTag(['name' => 'description', 'content' => Yii::$app->feiber->seo_description], 'description');
?>
<link href="/static/css/style.css" rel="stylesheet">

</head>
<?php $this->beginBody() ?>
<body>


    <header>
        <div class="box">
            <div class="logo"><a href="<?= Yii::$app->getHomeUrl() ?>" title="<?= Yii::$app->feiber->website_title ?>"><?= Yii::$app->feiber->website_title ?></a></div>
            <nav>
                <?= MenuView::widget() ?>
                <h2 id="mnavh"><span class="navicon"></span></h2>
                <div class="is-search"> <i></i></div>
            </nav>
        </div>
    </header>


    <?= $content ?>
    
    
    


    <footer>
        <div class="box">

            <div class="copyright">
                <p><a href="/"><?= Yii::$app->feiber->website_title ?></a> 版权所有 </p>
                <p>&nbsp;&nbsp;备案号<?= Yii::$app->feiber->website_icp ?></p>
            </div>
        </div>
    </footer>
    
    
    
    
</body>
<script type="text/javascript">
    window._deel = {name: '<?=Yii::$app->feiber->website_title?>', url: '<?=Yii::$app->getHomeUrl()?>', comment_url: '<?=Url::to(['article/comment'])?>', ajaxpager: '', commenton: 0, roll: [4,]}
</script>

<script src="/static/js/common.js"></script>

<?php
$this->endBody();
JsBlock::begin();
if (Yii::$app->feiber->website_statics_script) {
    echo Yii::$app->feiber->website_statics_script;
}
JsBlock::end();
?>
</html>
<?php $this->endPage() ?>