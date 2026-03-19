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

/**
 * This is the model class for table "{{%tag_index}}".
 *
 * @property integer $module
 * @property integer $aid
 * @property string  $key
 * @property string  $tid
 * @property integer $created_at
 */
class TagIndex extends \yii\db\ActiveRecord
{
    const MODULE_ARTICLE = 1;
    const MODULE_PAGE = 2;

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
        return '{{%tag_index}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['aid', 'tid', 'module'], 'required'],
            [['aid', 'tid', 'module', 'created_at'], 'integer'],
            [['key'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'module'     => '模块',
            'aid'        => Yii::t('app', 'Aid'),
            'key'        => Yii::t('app', 'Key'),
            'value'      => Yii::t('app', 'Value'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }
}
