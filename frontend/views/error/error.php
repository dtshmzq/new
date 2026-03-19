<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2017-03-15 21:16
 */

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<section class="single-page">
    <div class="whitebg">
        <h1 class="con_tilte"><?= Html::encode($this->title) ?></h1>
        <p align="center"><?= nl2br(Html::encode($message)) ?></p>
        <div class="con_text" style="text-align: center;">
            <p>
                <?= Yii::t('frontend', 'The above error occurred while the Web server was processing your request.') ?>
            </p>
            <p>
                <?= Yii::t('frontend', 'Please contact us if you think this is a server error. Thank you.') ?>
            </p>
        </div>
    </div>
</section>