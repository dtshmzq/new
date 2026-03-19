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
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$artWhere = ['status' => 1];
$artModel = Article::find()->where(['status' => 1]);
$newArts = $artModel->andWhere(['<>', 'id', 1])->limit(10)->orderBy('created_at desc')->cache(600)->all();
$noArtIds = ArrayHelper::getColumn($newArts, 'id');
?>
<aside class="rbox">
    <?= ArticleListView::widget(
        [
            'dataProvider' => new ArrayDataProvider(
                [
                    'allModels' => $newArts,
                ]
            ),
            'layout'       => '<h4 class="breadcrumbs">最新文章</h4><ul>{items}</ul>',
            'template'     => '<a href="{article_url}" title="{title}" target="_blank">{title}</a>',
            'itemOptions'  => ['tag' => 'li'],
            'options'      => ['class' => 'whitebg wenzi'],
        ]
    ) ?>
    <div class="whitebg paihang">
        <h4 class="breadcrumbs">站长推荐</h4>
        <?= ArticleListView::widget(
            [
                'dataProvider' => new ArrayDataProvider(
                    [
                        'allModels' => Article::find()->where(['status' => 1])->limit(10)->orderBy('id desc')->cache(7200)->all(),
                    ]
                ),
                'layout'       => '{items}',
                'template'     => '<i></i><a href="{article_url}" title="{title}" target="_blank">{title}</a>',
                'itemOptions'  => ['tag' => 'li'],
                'options'      => ['tag' => 'ul'],
            ]
        ) ?>
    </div>
</aside>