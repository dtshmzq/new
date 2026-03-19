<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2016-10-16 17:15
 */

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%spider_article_content}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string  $title
 * @property string  $content
 * @property Article $a
 */
class SpiderArticleContent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spider_article_content}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'integer'],
            [['title', 'content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'      => Yii::t('app', 'ID'),
            'title'   => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
        ];
    }
}
