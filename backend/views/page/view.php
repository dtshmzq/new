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
        'title',
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
        [
            'format' => 'raw',
            'attribute' => 'content',
            'value' => function($model){
                /** @var common\models\Page $model */
                return $model->content;
            }
        ],
        'created_at:datetime',
        'updated_at:datetime',
    ],
]) ?>