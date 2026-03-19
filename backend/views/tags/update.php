<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-03-24 12:51
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Tags'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Update') . Yii::t('app', 'Tags')],
];
/**
 * @var $model common\models\Tags
 */
?>
<?= $this->render('_form', [
    'model' => $model,
]);
