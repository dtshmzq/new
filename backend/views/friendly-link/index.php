<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-03-21 14:14
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $searchModel backend\models\search\FriendlyLinkSearch
 */

use backend\grid\column\DateColumn;
use backend\grid\view\GridView;
use backend\grid\column\SortColumn;
use backend\grid\column\StatusColumn;
use backend\widgets\Bar;
use yii\helpers\Html;
use backend\grid\column\CheckboxColumn;
use backend\grid\column\ActionColumn;
use common\libs\Constants;

$this->title = "Friendly Links";
$this->params['breadcrumbs'][] = Yii::t('app', 'Friendly Links');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'name'
                        ],
                        [
                            'attribute' => 'url',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a($model->url, $model->url, ['target' => '_blank']);
                            }
                        ],
                        [
                            'class' => SortColumn::className()
                        ],
                        [
                            'class' => StatusColumn::className(),
                            'filter' => Constants::getYesNoItems(),
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'attribute' => 'updated_at',
                        ],
                        [
                            'class' => ActionColumn::className(),
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>