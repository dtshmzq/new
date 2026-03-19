<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2018-02-24 22:33
 */

use common\libs\Constants;
use yii\widgets\DetailView;

/** @var $model common\models\Comment */
?>
<?=DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'key',
        'value',
        'count_num',
        'created_at:datetime',
    ]
])?>
