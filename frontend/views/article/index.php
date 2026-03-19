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
 * @var $rightAd2 \backend\models\form\AdForm![](C:/Users/Felix/AppData/Local/Temp/aeb68dc74a1047ceb207d87ac15aca24.png)
 * @var $headLinesArticles \common\models\Article
 */
use common\models\Article;
use frontend\widgets\ArticleListView;
use common\widgets\JsBlock;
use frontend\assets\IndexAsset;
use yii\data\ArrayDataProvider;
use frontend\widgets\FriendlyLinkView;

IndexAsset::register($this);
$page = Yii::$app->getRequest()->get('page');
if (isset($seo) && !empty($seo)) {
    $this->title = !empty($seo->seo_title) ? $seo->seo_title : $seo->name;
    !empty($seo->seo_keywords) && $this->registerMetaTag(['name' => 'keywords', 'content' => $seo->seo_keywords], 'keywords');
    !empty($seo->seo_description) && $this->registerMetaTag(['name' => 'description', 'content' => $seo->seo_description], 'description');
    (!empty($page) && intval($page) > 1) && $this->title .= "_第{$page}页";
    $this->title .= ' - ' . Yii::$app->feiber->website_title;
} else {
    $this->title = Yii::$app->feiber->website_title;
    if(!empty($page) && intval($page) > 1){
        $this->title .= " 第{$page}页";
    }else{
        $this->title;
    }
}
?>


    <div class="clear"></div>
    <div class="box">
        <div class="blogs">

            <?= str_replace('page_1.html', '', ArticleListView::widget(
						[
							'dataProvider' => $dataProvider,
							'options'      => ['tag' => null],
						]
					)) ?>


        </div>
        <?=$this->render('_index_sidebar')?>

    </div>
    <div class="box">
        <div class="links">
            <div class="linksconm">
                <div class="link_title">友情链接</div>
                <ul>
                    <?= FriendlyLinkView::widget([
                    'itemTemplate' => '<li ><a target="_blank" href="{%URL%}" title="{%NAME%}">{%NAME%}</a></li>',
                    'layout' => '{%ITEMS%}',
                    ]) ?>
                </ul>
            </div>
        </div>
    </div>