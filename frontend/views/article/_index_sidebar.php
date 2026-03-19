<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2016-06-21 14:26
 */

/**
 * @var $rightAd1 \backend\models\form\AdForm
 * @var $rightAd2 \backend\models\form\AdForm
 */

use common\models\Article;
use frontend\widgets\ArticleListView;
use yii\data\ArrayDataProvider;

$sidebarWhere = ['status' => 1, 'type' => 0];
?>
<aside class="rbox">



    <div class="hot_news">
        <div class="h_title">热门文章</div>
        <ol start="1">
            

            <?=ArticleListView::widget(
            [
                'dataProvider' => new ArrayDataProvider(
                    [
                        'allModels' => Article::find()->where(['>=', 'visit_nums', 2])->andWhere($sidebarWhere)->orderBy('RAND()')->limit(10)->cache(7200)->all(),
                    ]
                ),
                'layout'       => '{items}',
                'template'     => '<li>
                    <a href="{article_url}"   title="{title}" target="_blank">
                        <p>{title}</p>
                    </a>
                    <span>{visit_nums}</span>
                </li>',
                'itemOptions'  => ['tag' => false],
                'options'      => ['class' => false],
            ]
        )?>

        </ol>
    </div>
    <div class="isgood_news">
        <div class="h_title">最新文章</div>
        <ul>

            
            <?=ArticleListView::widget(
            [
                'dataProvider' => new ArrayDataProvider(
                    [
                    'allModels' => Article::find()
                    ->where(['status' => 1]) 
                    ->limit(9) 
                    ->orderBy(['created_at' => SORT_DESC]) 
                    ->cache(7200) 
                    ->all(),
                    ]
                ),
                'layout'       => '{items}',
                'template'     => '<li>
                    <a href="{article_url}"   title="{title}" target="_blank">
                        <p>{title}</p>
                    </a>
                    <span>{visit_nums}</span>
                </li>',
                'itemOptions'  => ['tag' => false],
                'options'      => ['class' => false],
            ]
        )?>


        </ul>
    </div>



</aside>

