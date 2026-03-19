<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2016-03-24 12:51
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Comments'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Update') . Yii::t('app', 'Comments')],
];
/**
 * @var $model common\models\Comment
 */
?>
<?= $this->render('_form', [
    'model' => $model,
]);
