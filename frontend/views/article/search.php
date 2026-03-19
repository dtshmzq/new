<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2017-03-15 21:16
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $type string
 * @var $seo []
 * @var $indexBanners []
 */

/**
 * @var $rightAd1 \backend\models\form\AdForm
 * @var $rightAd2 \backend\models\form\AdForm
 * @var $headLinesArticles \common\models\Article
 */

use frontend\widgets\ArticleListView;
use frontend\widgets\ScrollPicView;
use common\widgets\JsBlock;
use frontend\assets\IndexAsset;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

IndexAsset::register($this);

$page = Yii::$app->getRequest()->get('page');

$this->title = !empty($seo->seo_title) ? $seo->seo_title : $seo->name;
!empty($seo->seo_keywords) && $this->registerMetaTag(['name' => 'keywords', 'content' => $seo->seo_keywords], 'keywords');
$this->registerMetaTag(['name' => 'description', 'content' => $seo->seo_description], 'description');

(!empty($page) && intval($page) > 1) && $this->title .= " 第{$page}页";
$this->title .= ' - ' . Yii::$app->feiber->website_title;
?>
<section class="box">
    <div class="lbox">
        <div class="whitebg ixfsh_list">
            <h4 class="breadcrumbs">
                <?= $type ?>
            </h4>
            <?= str_replace('page_1.html', '', ArticleListView::widget(
                [
                    'dataProvider' => $dataProvider,
                ]
            )) ?>
        </div>
    </div>
    <?=$this->render(
        '_sidebar',
        [
            'rightAd1' => $rightAd1,
            'rightAd2' => $rightAd2,
        ]
    )?>
</section>