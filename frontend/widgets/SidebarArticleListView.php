<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2020-02-20 01:30
 */

namespace mobile\widgets;

use common\helpers\StringHelper;
use Yii;
use yii\helpers\Url;

class SidebarArticleListView extends \yii\widgets\ListView
{
    /**
     * @var string 布局
     */
    public $layout = "{items}";

    /**
     * @var int 标题截取长度
     */
    public $titleLength = 28;

    /**
     * @var int seo_description截取长度
     */
    public $seoDescriptionLength = 120;

    /**
     * @var int 缩率图宽
     */
    public $thumbWidth = 220;

    /**
     * @var int 缩略图高
     */
    public $thumbHeight = 150;

    public $itemOptions = [
        'tag'   => 'li',
        'class' => '',
    ];

    /**
     * @var string 模板
     */
    public $template = '<a href="{%HREF%}" title="{%TITLE%}">{%TITLE%}</a>';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->itemView)) {
            $this->itemView = function ($model, $key, $index) {
                /** @var $model \common\models\Article */
                $title = $model->title;
                $seoDescription = \yii\helpers\StringHelper::truncate($model->seo_description, $this->seo_descriptionLength);
                $thumb = $model->getThumbUrlBySize();
                $href = empty($model->link_url) ? Url::to(['article/view', 'cat' => $model->category->alias, 'id' => $model->id]) : "/news/{$model->link_url}";

                return str_replace(
                    [
                        '{%HREF%}',
                        '{%THUMB%}',
                        '{%TITLE%}',
                        '{%seoDescription%}',
                        '{%VISIT_NUMS%}',
                        '{%PUB_DATE%}',
                    ],
                    [
                        $href,
                        $thumb,
                        $title,
                        $seoDescription,
                        $model->visit_nums,
                        date('Y-m-d', $model->created_at),
                    ],
                    $this->template
                );
            };
        }
    }
}