<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2016-06-19 00:21
 */

namespace frontend\widgets;

use yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use yii\helpers\StringHelper;

class ArticleListView extends \yii\widgets\ListView
{
    /**
     * @var string 布局
     */
    public $layout = "<ul>{items}</ul>\n<div class=\"pagination\"><ul>{pager}</ul></div>";

    /**
     * @var int 标题截取长度
     */
    public $titleLength = 28;

    /**
     * @var int seo_description截取长度
     */
    public $seoDescriptionLength = 70;

    /**
     * @var int 缩率图宽
     */
    public $thumbWidth = 200;

    /**
     * @var int 缩略图高
     */
    public $thumbHeight = 136;

    public $itemOptions = [
        'tag' => false
    ];

    public $pagerOptions = [
        'firstPageLabel' => '首页',
        'lastPageLabel'  => '尾页',
        'prevPageLabel'  => '上一页',
        'nextPageLabel'  => '下一页',
        'options'        => [
            'class' => '',
        ],
    ];

    public $template = '    <li class="blogs_list">
                                <a href="{article_url}" target="_blank">
                                    <em>{category}</em>
                                    <i>
                                        <img src="{img_url}" alt="{title}">
                                    </i>
                                    <h2>{title}</h2>
                                    <p>{truncate_title}...</p>
                                    <div class="blogs_base"><span class="blogs_time">{pub_date}</span><span
                                            class="blogs_onclick">{visit_nums}</span></div>
                                </a>
                            </li>
                        ';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->pagerOptions = [
            'firstPageLabel' => yii::t('app', 'first'),
            'lastPageLabel'  => yii::t('app', 'last'),
            'prevPageLabel'  => false,
            'nextPageLabel'  => false,
            'options'        => [
                'class' => 'th_page th_page_color',

            ],
        ];
        if (empty($this->itemView)) {
            $this->itemView = function ($model, $key, $index) {
                /** @var $model \common\models\Article */
                $categoryName = $model->category ? $model->category->name : yii::t('app', 'UnClassified');
                $categoryAlias = $model->category ? $model->category->alias : yii::t('app', 'UnClassified');
                $categoryUrl = Url::to(['article/list', 'cat' => $categoryAlias]);
                $imgUrl = $model->getThumbUrlBySize($this->thumbWidth, $this->thumbHeight);
                $articleUrl = Url::to(['article/view', 'id' => $model->id]);
                $seoDescription = StringHelper::truncate($model->seo_description, $this->seoDescriptionLength);
                $title = $model->title;
                $truncateTitle = StringHelper::truncate($model->title, $this->titleLength);

                return str_replace(
                    [
                        '{article_url}',
                        '{img_url}',
                        '{category_url}',
                        '{title}',
                        '{truncate_title}',
                        '{seo_description}',
                        '{pub_date}',
                        '{visit_nums}',
                        '{category}',
                    ],
                    [
                        $articleUrl,
                        $imgUrl,
                        $categoryUrl . '/',
                        $title,
                        $truncateTitle,
                        $seoDescription,
                        date('Y-m-d', $model->created_at),
                        $model->visit_nums * 100,
                        $categoryName,
                    ],
                    $this->template
                );
            };
        }
    }

    /**
     * @inheritdoc
     */
    public function renderPager()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['maxButtonCount'] = 3;
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();
        $pager = array_merge($pager, $this->pagerOptions);

        return $class::widget($pager);
    }

}
