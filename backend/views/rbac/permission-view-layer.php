<?php
/**
 * Author: feiber
 * Blog: 
 * Email:
 * Created at: 2018-02-24 23:02
 */

use yii\widgets\DetailView;

/** @var $model backend\models\form\RBACPermissionForm */
?>
<?=DetailView::widget([
    'model' => $model,
    'attributes' => [
        'group',
        'category',
        'route',
        'method',
        'description',
        'sort',
    ],
])?>
