<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2020-02-20 11:30
 */

/** @var $model \common\models\Article */

$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords], 'keywords');
$this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description], 'description');