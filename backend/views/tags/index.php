<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-03-23 17:51
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider common\models\Comment
 * @var $searchModel backend\models\search\TagsSearch
 */

use common\libs\Constants;
use yii\widgets\Pjax;
use backend\widgets\Bar;
use backend\grid\view\GridView;
use backend\grid\column\CheckboxColumn;
use backend\grid\column\ActionColumn;
use backend\grid\column\DateColumn;
use backend\grid\column\StatusColumn;

$this->title = 'Tags';
$this->params['breadcrumbs'][] = Yii::t('app', 'Tags');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget(['template' => "{refresh} {delete}",]) ?>
                <?php Pjax::begin(['id' => 'pjax', 'timeout' => 30000]); ?>
                <?= GridView::widget(
                    [
                        'dataProvider' => $dataProvider,
                        'filterModel'  => $searchModel,
                        'columns'      => [
                            [
                                'class' => CheckboxColumn::className(),
                            ],
                            [
                                'attribute' => 'id',
                            ],
                            [
                                'attribute' => 'key',
                            ],
                            [
                                'attribute' => 'value',
                            ],
                            [
                                'attribute' => 'count_num',
                            ],
                            [
                                'class'     => StatusColumn::className(),
                                'attribute' => 'flag_recommend',
                                'filter'    => Constants::getYesNoItems(),
                            ],
                            [
                                'class'     => DateColumn::className(),
                                'attribute' => 'created_at',
                            ],
                            [
                                'class'    => ActionColumn::className(),
                                'width'    => '190px',
                                'template' => '{create} {view-layer} {update} {delete}',
                            ],
                        ],
                    ]
                ); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>