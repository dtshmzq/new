<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2020-02-20 01:30
 */

namespace frontend\widgets;

use Yii;
use yii\helpers\Url;
use common\models\Tags;
use common\models\meta\TagIndexArticle;

class HottestArticleTagView extends \yii\base\Widget
{
    public $data = null;

    public $layout = "{%ITEMS%}";

    public $itemTemplate = "<a title='' href='{%HREF%}' target='_blank' data-original-title='{%TAG_NUM%}{%TOPICS%}'> {%TAG_NAME%} ({%TAG_NUM%})</a>";

    public function run()
    {
        $items = "";
        $data = $this->getData();
        foreach ($data as $key => $val) {
            $item = str_replace("{%HREF%}", Url::to(['search/tag', 'id' => $val['id']]), $this->itemTemplate);
            $item = str_replace("{%TAG_NUM%}", $val['count_num'], $item);
            $item = str_replace("{%TOPICS%}", " " . Yii::t('frontend', 'Topics'), $item);
            $item = str_replace("{%TAG_NAME%}", $val['value'], $item);
            $items .= $item;
        }

        return str_replace("{%ITEMS%}", $items, $this->layout);
    }

    private function getData()
    {
        if ($this->data === null) {
            $this->data = (new Tags())->getHottestTags((new TagIndexArticle())->keyName, 100);
        }

        return $this->data;
    }
}