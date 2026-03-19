<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2017-03-15 21:16
 */

namespace common\models\meta;

use Yii;

class TagIndexLike extends \common\models\TagIndex
{
    public $keyName = "like";

    /**
     * @param $aid
     * @return bool
     */
    public function setLike($aid)
    {
        $this->aid = $aid;
        $this->key = $this->keyName;
        $this->tid = ip2long(Yii::$app->getRequest()->getUserIP());

        return $this->save(false);
    }

    /**
     * @param $aid
     * @return int|string
     */
    public function getLikeCount($aid)
    {
        return $this->find()->where(['aid' => $aid, 'key' => $this->keyName])->count("aid");
    }

}
