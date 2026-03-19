<?php
/**
 * Author: feiber
 * Blog: 
 * Email:
 * Created at: 2016-03-23 15:47
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Comments'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Create') . Yii::t('app', 'Comments')],
];
/**
 * @var $model common\models\Comment
 */
?>
<?= $this->render('_form', [
    'model' => $model,
]);
