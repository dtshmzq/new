<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-10-16 17:15
 */

namespace common\models;

use common\helpers\Util;
use common\models\meta\TagIndexArticle;
use common\models\meta\TagIndexImages;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%page}}".
 * @property integer $id
 * @property integer $cid
 * @property string  $title
 * @property string  $seo_title
 * @property string  $seo_keywords
 * @property string  $seo_description
 * @property integer $status
 * @property integer $sort
 * @property string  $template
 * @property integer $created_at
 * @property integer $updated_at
 * @property string  $content
 */
class Page extends \yii\db\ActiveRecord
{
    /**
     * @var string
     */
    public $tag = '';

    /**
     * @var array
     */
    public $images;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'status', 'sort', 'author_id'], 'integer'],
            [['cid', 'sort', 'author_id'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['title', 'status', 'content'], 'required'],
            [['images'], 'safe'],
            [['created_at', 'updated_at'], 'safe'],
            [
                [
                    'title',
                    'seo_title',
                    'seo_keywords',
                    'seo_description',
                    'tag',
                    'template',
                ],
                'string',
                'max' => 255,
            ],
            [['content'], 'string'],
            [
                [
                    'status',
                ],
                'in',
                'range' => [0, 1],
            ],
            [['cid', 'sort'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('app', 'ID'),
            'title'           => Yii::t('app', 'Title'),
            'sub_title'       => Yii::t('app', 'Sub Title'),
            'thumb'           => Yii::t('app', 'Thumb'),
            'seo_title'       => Yii::t('app', 'Seo Title'),
            'seo_keywords'    => Yii::t('app', 'Seo Keyword'),
            'seo_description' => Yii::t('app', 'Seo Description'),
            'status'          => Yii::t('app', 'Status'),
            'sort'            => Yii::t('app', 'Sort'),
            'tag'             => Yii::t('app', 'Tag'),
            'author_id'       => Yii::t('app', 'Author Id'),
            'author_name'     => Yii::t('app', 'Author'),
            'created_at'      => Yii::t('app', 'Created At'),
            'updated_at'      => Yii::t('app', 'Updated At'),
            'template'        => Yii::t('app', 'Article Template'),
            'visit_nums'      => Yii::t('app', 'Visit Nums'),
            'comment_count'   => Yii::t('app', 'Comment Count'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        (new TagIndexArticle())->setArticleTags($this->id, $this->tag, TagIndex::MODULE_PAGE);
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $insert = $this->getIsNewRecord();
        $this->seo_keywords = str_replace('，', ',', $this->seo_keywords);

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!empty($this->thumb)) {
            Util::getInstance()->deleteThumbnails(Yii::getAlias('@frontend/web') . $this->thumb, self::$thumbSizes, true);
        }
        (new TagIndexArticle())->setArticleTags($this->id, []);

        return parent::beforeDelete();
    }
}
