<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2017-03-15 21:16
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%spider_log}}".
 *
 * @property integer $id
 * @property string  $spider
 * @property string  $user_agent
 * @property string $ip
 * @property string  $url
 * @property integer $created_at
 */
class SpiderLog extends \yii\db\ActiveRecord
{
    const ARTICLE_VIEW = 5;

    public static function typeItems()
    {
        return ['-' => '-', '360' => '360', 'baidu' => 'baidu', 'sogou' => 'sogou', 'bing' => 'bing', 'shenma' => 'shenma', 'toutiao' => 'toutiao', 'x' => 'x'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spider_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_agent', 'spider'], 'string'],
            [['created_at'], 'integer'],
            [['ip', 'url'], 'string', 'max' => 255],
            [['spider'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'spider'     => 'Spider',
            'user_agent' => 'User Agent',
            'ip'         => 'IP',
            'url'        => 'URL',
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert && empty($this->created_at)) {
            $this->created_at = time();
        }

        return parent::beforeSave($insert);
    }
}