<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-03-23 15:47
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Tags'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Create') . Yii::t('app', 'Tags')],
];
/**
 * @var $model common\models\Tags
 */
?>
<?= $this->render('_form', [
    'model' => $model,
]);
