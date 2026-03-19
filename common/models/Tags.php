<?php
/**
 * Author: feiber
 * Blog: 
 * Email:
 * Created at: 2016-10-16 17:15
 */

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\libs\Constants;

/**
 * This is the model class for table "{{%tags}}".
 *
 * @property string $id
 * @property string $key
 * @property string $value
 * @property string $count_num
 * @property integer $flag_recommend
 * @property string $created_at
 */
class Tags extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class'              => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'required'],
            [['count_num', 'created_at'], 'integer'],
            [['key'], 'string', 'max' => 20],
            [['value'], 'string', 'max' => 50],
            [['flag_recommend'], 'in', 'range' => [0, 1]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => Yii::t('app', 'ID'),
            'key'            => Yii::t('app', 'Key'),
            'value'          => '标签名',
            'count_num'      => '数量',
            'flag_recommend' => Yii::t('app', 'Is Recommend'),
            'created_at'     => Yii::t('app', 'Created At'),
        ];
    }

    public function getHottestTags($keyName, $limit = 12)
    {
        $tags = self::find()
                    ->select(['id', 'value', 'count_num'])
                    ->where(['key' => $keyName])
                    ->andWhere(['>', 'count_num', Constants::YesNo_No])
                    ->limit($limit)
                    ->asArray()
                    ->all();

        return $tags;
    }
}
