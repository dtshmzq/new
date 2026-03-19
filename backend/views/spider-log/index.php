<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-03-23 17:51
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider common\models\SpiderLog
 * @var $searchModel backend\models\search\SpiderLogSearch
 */

use backend\grid\column\DateColumn;
use backend\grid\view\GridView;
use backend\widgets\Bar;
use backend\grid\column\CheckboxColumn;
use backend\grid\column\ActionColumn;
use common\models\SpiderLog;
use yii\helpers\Html;

$this->title = "Spider Log";

$this->params['breadcrumbs'][] = Yii::t('app', 'Spider Log');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget(
                    [
                        'template' => '{refresh} {delete}',
                    ]
                ) ?>
                <?= GridView::widget(
                    [
                        'dataProvider' => $dataProvider,
                        'filterModel'  => $searchModel,
                        'columns'      => [
                            [
                                'attribute' => 'id',
                                'width'     => '40px',
                            ],
                            [
                                'attribute' => 'spider',
                                'width'     => '50px',
                                'filter'    => SpiderLog::typeItems(),
                            ],
                            [
                                'attribute' => 'url',
                                'width' => '80px',
                                'format'    => 'raw',
                                'value'     => function ($model, $key, $index, $column) use ($frontendURLManager) {
                                    /** @var common\models\Article $model */
                                    $scriptName = "";
                                    if ($frontendURLManager->showScriptName) {
                                        $scriptName = "index.php/";
                                    }
                                    $url = Yii::$app->feiber->website_url . $model->url;

                                    return Html::a($model->url, $url, ['target' => '_blank', 'data-pjax' => 0]);
                                },
                            ],
                            [
                                'attribute' => 'user_agent',
                                'width'     => '300px',
                            ],
                            [
                                'attribute' => 'ip',
                                'width' => '70px',
                            ],
                            [
                                'class' => DateColumn::className(),
                                'headerOptions' => ['width' => '60px'],
                                'format'    => ['datetime', 'php:m-d H:i'],
                                'attribute' => 'created_at',
                            ],
                            [
                                'class'    => ActionColumn::className(),
                                'template' => '{view-layer} {delete}',
                            ],
                        ],
                    ]
                ); ?>
            </div>
        </div>
    </div>
</div>