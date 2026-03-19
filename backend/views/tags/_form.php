<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2016-03-23 15:49
 */

/**
 * @var $this yii\web\View
 * @var $model common\models\Tags
 */

use backend\widgets\ActiveForm;

$this->title = "Tags";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?=$this->render('/widgets/_ibox-title')?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'key') ?>
                <div class="hr-line-dashed"></div>
                <?= $form->field($model, 'value')?>
                <div class="hr-line-dashed"></div>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>