<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2017-12-03 21:38
 */
/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ArrayDataProvider
 * @var $searchModel \backend\models\search\OptionsSearch
 */

use backend\grid\view\GridView;
use backend\widgets\Bar;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\grid\column\CheckboxColumn;
use backend\grid\column\ActionColumn;

$this->title = "Banner Types";
$this->params['breadcrumbs'][] = Yii::t('app', 'Banner Types');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget()?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'layout' => '{items}',
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'attribute' => 'name',
                            'label' => Yii::t("app", 'Name'),
                        ],
                        [
                            'attribute' => 'tips',
                            'label' => Yii::t("app", "Description")
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'width' => '190px',
                            'buttons' => [
                                'entry' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa fa-bars" aria-hidden="true"></i> ', Url::to([
                                        'banners',
                                        'id' => $model['id']
                                    ]), [
                                        'title' => Yii::t('app', 'Entry'),
                                        'data-pjax' => '0',
                                        'class' => 'btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'template' => '{entry} {update} {delete}',
                        ]
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>