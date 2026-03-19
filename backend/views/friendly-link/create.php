<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2016-03-21 14:32
 */

use yii\helpers\Url;

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Friendly Links'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Create') . Yii::t('app', 'Friendly Links')],
];
/**
 * @var $model common\models\FriendlyLink
 */
?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>