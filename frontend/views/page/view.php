<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2016-06-21 11:07
 */

/**
 * @var $this yii\web\View
 * @var $singlePages []common\models\Page
 */

use common\services\PageService;
use yii\helpers\Url;

!empty($model->seo_keywords) && $this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords], 'keywords');
!empty($model->seo_description) && $this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description], 'description');
$this->title = $model->title . '-' . Yii::$app->feiber->website_title;
$singlePages = (new PageService)->getSinglePages();
?>
<section class="single-page">
    <div class="whitebg">
        <h1 class="con_tilte"><?= $model->title ?></h1>
        <div class="con_text">
            <?= $model->content ?>
        </div>
    </div>
</section>
