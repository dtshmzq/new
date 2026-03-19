<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-04-03 21:12
 */

use yii\helpers\Url;

/**
 * @var $categories []
 */

$this->params['breadcrumbs'] = [
    ['label' => Yii::t('app', 'Category'), 'url' => Url::to(['index'])],
    ['label' => Yii::t('app', 'Update') . Yii::t('app', 'Category')],
];
/**
 * @var $model common\models\Category
 */
?>
<?= $this->render('_form', [
    'model' => $model,
    'categories' => $categories,
]); ?>