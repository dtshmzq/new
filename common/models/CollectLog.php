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
 * This is the model class for table "{{%collect_log}}".
 *
 * @property integer $id
 * @property string  $spider
 * @property string  $user_agent
 * @property integer $ip
 * @property string  $url
 * @property string $created_at
 */
class CollectLog extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%collect_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['url_md5'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'url_md5'    => 'Url',
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_at = date('Y-m-d H:i:s', time());
        }

        return parent::beforeSave($insert);
    }
}