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

IndexAsset::register($this);

$page = Yii::$app->getRequest()->get('page');

$this->title = !empty($seo->seo_title) ? $seo->seo_title : $seo->name;
!empty($seo->seo_keywords) && $this->registerMetaTag(['name' => 'keywords', 'content' => $seo->seo_keywords], 'keywords');
!empty($seo->seo_description) && $this->registerMetaTag(['name' => 'description', 'content' => $seo->seo_description], 'description');

(!empty($page) && intval($page) > 1) && $this->title .= " 第{$page}页";
$this->title .= ' - ' . Yii::$app->feiber->website_title;
?>

    <div class="clear"></div>
    <div class="box">
        <div class="blogs">


            <div class="breadcrumb">
                <a href="<?= Yii::$app->getHomeUrl() ?>">首页</a>
                >
                <a href="/<?=$seo->alias?>/" title="<?= $type ?>" target="_blank"><?= $type ?></a>
            </div>
            <?= str_replace('page_1.html', '', ArticleListView::widget(
						[
							'dataProvider' => $dataProvider,
							'options'      => ['tag' => null],
						]
					)) ?>


        </div>
        <?=$this->render('_index_sidebar')?>
    </div>