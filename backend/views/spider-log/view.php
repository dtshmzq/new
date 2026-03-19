<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2016-04-14 10:09
 */

use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model common\models\SpiderLog
 */

$this->title = "Spider Log Detail";
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'user_agent',
        [
            'label' => 'ip',
            'attribute' => 'ip',
            'value' => function($model){
                return long2ip($model->ip);
            }
        ],
        'url',
        'created_at:datetime',
        [
            'attribute' => 'description',
            'format' => 'raw',
        ]
    ],
]) ?>