<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2016-04-14 12:09
 */

use common\libs\Constants;
use yii\widgets\DetailView;

/**
 * @var $model common\models\Article
 */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        [
            'attribute' => 'category',
            'value' => function($model){
                return $model->category === null ? "-" : $model->category->name;
            }
        ],
        'title',
        'seo_title',
        [
            'attribute' => 'thumb',
            'format' => 'raw',
            'value' => function($model){
                return "<img style='max-width:200px;max-height:200px' src='" . $model->thumb . "' >";
            }
        ],
        'seo_title',
        'seo_keywords',
        'seo_description',
        [
            'attribute' => 'status',
            'value' => function($model){
                return Constants::getStatusItems($model->status);
            }
        ],
        'sort',
        'template',
        [
            'format' => 'raw',
            'attribute' => 'content',
            'value' => function($model){
                /** @var \common\models\Article $model */
                return $model->articleContent->content;
            }
        ],
        'created_at:datetime',
        'updated_at:datetime',
    ],
]) ?>