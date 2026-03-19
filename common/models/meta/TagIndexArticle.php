<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2017-10-08 00:30
 */

namespace common\models\meta;

use common\models\Article;
use common\models\TagIndex;
use common\models\Tags;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

class TagIndexArticle extends \common\models\TagIndex
{
    public $keyName = 'tag';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aid'], 'required'],
            [['tag'], 'string', 'max' => 255],
        ];
    }

    public function setArticleTags(int $aid, $tags, $module = self::MODULE_ARTICLE)
    {
        if (!is_string($tags) && empty($tags)) {
            return false;
        }
        $tags = str_replace(['，', '、'], ',', strtolower($tags));
        $tags = array_unique(array_filter(explode(',', $tags)));
        $oldTags = $this->getTagsByArticle($aid, $module);

        $needAdds = array_diff($tags, $oldTags);
        $needRemoves = array_diff($oldTags, $tags);
        if (!empty($needAdds)) {
            foreach ($needAdds as $tag) {
                $whereData = ['value' => trim($tag), 'key' => $this->keyName];
                $data = Tags::find()->where($whereData)->one();
                if (empty($data)) {
                    $articleTag = new Tags($whereData);
                    $rtn = $articleTag->save();
                    if (empty($rtn)) {
                        throw new Exception("save article error");
                    }
                    $tid = $articleTag->id;
                } else {
                    $tid = $data['id'];
                    Tags::updateAllCounters(['count_num' => 1], 'id=' . $data['id']);
                }
                $metaModel = new TagIndex(['tid' => $tid, 'aid' => $aid, 'module' => $module, 'key' => $this->keyName]);
                $metaModel->save();
            }
        }

        if (!empty($needRemoves)) {
            foreach ($needRemoves as $tag) {
                $data = Tags::find()->where(['value' => trim($tag), 'key' => $this->keyName])->one();
                if (!empty($data)) {
                    TagIndex::deleteAll(['tid' => $data['id'], 'aid' => $aid, 'module' => $module, 'key' => $this->keyName]);
                    $articleTag = Tags::find()->where(['id' => $data['id']])->one();
                    if ($articleTag->count_num <= 1) {
                        $articleTag->delete();
                    } else {
                        $articleTag->count_num--;
                        $articleTag->save();
                    }
                }
            }
        }

        foreach ($tags as $tk => $tag) {
            $tid = Tags::find()->select(['id'])->where(['value' => $tag, 'key' => $this->keyName])->scalar();

            TagIndex::updateAll(['created_at' => time() + $tk], ['tid' => $tid, 'aid' => $aid, 'key' => $this->keyName, 'module' => $module]);
        }
    }

    public function getTagsByArticle($aid, $module = self::MODULE_ARTICLE, $isString = false)
    {
        $result = $this->find()
                       ->alias('ti')
                       ->select('t.value')
                       ->leftJoin(Tags::tableName() . ' as t', 'ti.tid = t.id')
                       ->where(['ti.aid' => $aid, 'ti.module' => $module, 'ti.key' => $this->keyName])
                       ->orderBy('ti.created_at asc')
                       ->asArray()
                       ->all();
        if ($result === null) {
            return ($isString) ? '' : [];
        }

        $result = ArrayHelper::getColumn($result, 'value');
        if ($isString) {
            return implode(',', $result);
        }

        return $result;
    }

    public function getAidsByTag($tid, $module = self::MODULE_ARTICLE)
    {
        $result = self::find()->where(['tid' => $tid, 'module' => $module, 'key' => $this->keyName])->cache(300)->asArray()->all();
        if ($result === null) return [];

        return ArrayHelper::getColumn($result, 'aid');
    }

    public function getAidByTags($aid, $module = self::MODULE_ARTICLE)
    {
        $result = self::find()
                      ->alias('ti')
                      ->select(['t.value', 't.id'])
                      ->leftJoin(Tags::tableName() . ' as t', 'ti.tid = t.id')
                      ->where(['ti.aid' => $aid, 'ti.module' => $module, 'ti.key' => $this->keyName])
                      ->orderBy('ti.created_at asc')
                      ->cache(3700)
                      ->asArray()
                      ->all();
        if ($result === null) return [];

        return $result;
    }
}