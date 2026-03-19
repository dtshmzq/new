<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2016-04-02 22:55
 */

/**
 * @var $this yii\web\View
 * @var $model common\models\Article
 * @var $commentModel common\models\Comment
 * @var $prev common\models\Article
 * @var $next common\models\Article
 * @var $recommends array
 * @var $recommendsTxt array
 * @var $commentList array
 */

/**
 * @var $rightAd1 \backend\models\form\AdForm
 * @var $rightAd2 \backend\models\form\AdForm
 */

use frontend\widgets\ArticleListView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use frontend\assets\ViewAsset;
use common\widgets\JsBlock;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$categoryName = $model->category ? $model->category->name : Yii::t('app', 'UnClassified');
$categoryAlias = $model->category ? $model->category->alias : Yii::t('app', 'UnClassified');

$this->title = (empty($model->seo_title) ? $model->title : $model->seo_title). '_' . Yii::$app->feiber->website_title;


ViewAsset::register($this);
?>

<?= $this->render("_register_meta_tags", ['model' => $model]) ?>
    <div class="clear"></div>
    <div class="box">

        <div class="blogs_info_page">


            <div class="breadcrumb">
                <a href="<?= Yii::$app->getHomeUrl() ?>" title="首页">首页</a>
                >
                <a href="<?= Url::to(['article/list', 'cat' => $categoryAlias]) ?>/" title="<?= $categoryName ?>">
                    <?= $categoryName ?>
                </a>
                >
                正文
            </div>
            <article>
                <h1>
                    <?= $model->title ?>
                </h1>
                <div class="wz_info"><span>时间：
                        <?= Yii::$app->getFormatter()->asDatetime($model->created_at, 'Y-MM-dd H:mm') ?>
                    </span> <span>阅读数：
                        <?= $model->visit_nums ?>人阅读
                    </span></div>
                <div class="content">
                    <script src="/static/js/ad1.js"></script>
                    <?php
                    $content = $model->articleContent->content ?? '';
                    if (!empty($content)) {
                        $imgNumber = rand(1001, 1175);
                        $imgSrc = '/static/background/' . $imgNumber . '.jpg';
                        $imgTag = "<div style='margin: 10px 0;'><img src='{$imgSrc}' style='max-width: 100%; height: auto;' alt='随机配图' /></div>";
                        $contentLength = mb_strlen($content, 'UTF-8');
                        if ($contentLength <= 50) {
                            $insertPos = floor($contentLength / 2);
                        } else {
                            $insertPos = rand(20, $contentLength - 20);
                        }
                        $content = mb_substr($content, 0, $insertPos, 'UTF-8') 
                                 . $imgTag 
                                 . mb_substr($content, $insertPos, null, 'UTF-8');
                    }
                    
                    echo $content;
                    ?>
                    <script src="/static/js/ad2.js"></script>
                </div>

                <div class="info-pre-next">
                    <p>上一篇:

                        <?php if ($prev !== null) { ?>
                            <a href='<?=Url::to(['article/view', 'id' => $prev->id])?>' rel="prev"><?=$prev->title?></a>
                            <?php }else{ ?>
                            <a href="<?=Url::to(['article/list', 'cat' => $categoryAlias]) ?>/">返回<?=$categoryName?>栏目</a>
                            <?php } ?>
                    </p>

                    <p>下一篇:

                        <?php if ($next != null) { ?>
                            <a href="<?= Url::to(['article/view', 'id' => $next->id]) ?>" rel="next"><?= $next->title ?></a>
                            <?php }else{ ?>
                            <a href="<?=Url::to(['article/list', 'cat' => $categoryAlias]) ?>/">返回<?=$categoryName?>栏目</a>
                            <?php } ?>

                    </p>
                </div>
            </article>


        </div>
        <?=$this->render('_index_sidebar')?>
    </div>