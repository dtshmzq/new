<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-03-31 14:17
 */

use common\widgets\JsBlock;
use yii\helpers\Url;

/**
 * @var $statics array
 * @var $this yii\web\View
 * @var array $comments latest comments
 */
$this->registerCss("
     .environment .list-group-item > .badge {float: left}
     .environment  li.list-group-item strong {margin-left: 15px}
     ul#notify .list-group-item{line-height:15px}
")
?>
<div class="row">
    <div class="col-sm-3">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <span class="label label-success pull-right"><?= Yii::t('app', 'Month') ?></span>
                <h5><?= Yii::t('app', 'Articles') ?></h5>
            </div>
            <div class="ibox-content openContab" href="<?=Url::to(['article/index'])?>" title="<?= Yii::t('app', 'Articles')?>" style="cursor: pointer">
                <h1 class="no-margins"><?= $statics['ARTICLE'][0] ?></h1>
                <div class="stat-percent font-bold text-success"><?= $statics['ARTICLE'][1] ?>% <i class="fa fa-bolt"></i></div>
                <small><?= Yii::t('app', 'Total') ?></small>
            </div>
        </div>
    </div>
</div>
