<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-10-16 17:15
 */

namespace common\models;

use Yii;
use yii\web\UploadedFile;
use yii\behaviors\TimestampBehavior;
use common\helpers\Util;
use common\models\meta\TagIndexImages;
use common\models\meta\TagIndexArticle;
use common\libs\Constants;

/**
 * This is the model class for table "{{%article}}".
 * @property integer        $id
 * @property integer        $cid
 * @property integer        $type
 * @property string         $title
 * @property string         $thumb
 * @property string         $seo_keywords
 * @property string         $seo_description
 * @property integer        $status
 * @property integer        $sort
 * @property string         $template
 * @property integer        $created_at
 * @property integer        $updated_at
 * @property ArticleContent $articleContent
 * @property Category       $category
 */
class Article extends \yii\db\ActiveRecord
{
    const ARTICLE = 0;
    const SINGLE_PAGE = 2;

    const ARTICLE_PUBLISHED = 1;
    const ARTICLE_DRAFT = 0;

    /**
     * @var string
     */
    public $tag = '';

    /**
     * @var array
     */
    public $viewTags = [];

    /**
     * 需要截取的文章缩略图尺寸
     */
    public static $thumbSizes = [
        ["w" => 192, "h" => 120],
    ];

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
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cid', 'type', 'status', 'sort'], 'integer'],
            [['cid', 'sort'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['title', 'status'], 'required'],
            [['thumb'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, webp'],
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
            [
                [
                    'status',
                ],
                'in',
                'range' => [0, 1],
            ],
            [['type'], 'default', 'value' => self::ARTICLE, 'on' => 'article'],
            [['type'], 'default', 'value' => self::SINGLE_PAGE, 'on' => 'page'],
            ['cid', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'                     => Yii::t('app', 'ID'),
            'cid'                    => Yii::t('app', 'Category Id'),
            'type'                   => Yii::t('app', 'Type'),
            'title'                  => Yii::t('app', 'Title'),
            'thumb'                  => Yii::t('app', 'Thumb'),
            'seo_title'              => Yii::t('app', 'Seo Title'),
            'seo_keywords'           => Yii::t('app', 'Seo Keyword'),
            'seo_description'        => Yii::t('app', 'Seo Description'),
            'status'                 => Yii::t('app', 'Status'),
            'sort'                   => Yii::t('app', 'Sort'),
            'tag'                    => Yii::t('app', 'Tag'),
            'created_at'             => Yii::t('app', 'Created At'),
            'updated_at'             => Yii::t('app', 'Updated At'),
            'template'               => Yii::t('app', 'Article Template'),
            'category'               => Yii::t('app', 'Category'),
            'images'                 => Yii::t('app', 'Article Images'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'cid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleContent()
    {
        return $this->hasOne(ArticleContent::className(), ['aid' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $insert = $this->getIsNewRecord();
        Util::getInstance()->handleModelSingleFileUpload($this, 'thumb', $insert, '@thumb', ['thumbSizes' => self::$thumbSizes]);
        $this->created_at = time();
        $this->title = Util::getInstance()->titleReplace($this->title);

        if (!empty($this->seo_keywords)) {
            $this->seo_keywords = str_replace(['，', '、'], ',', $this->seo_keywords);
        }
        if (!empty($this->seo_description)) {
            mb_internal_encoding("UTF-8");
            $encoding = mb_internal_encoding();
            $this->seo_description = preg_replace("/(\r\n|\n|\r|\t)/i", '', $this->seo_description);
            $this->seo_description = str_replace(['，', '、', '？', '！', '；'], ',', $this->seo_description);
            $this->seo_description = str_replace(['"', '“', '”'], '', $this->seo_description);
            $this->seo_description = Util::getInstance()->mbRtrim($this->seo_description, ',', $encoding);
        }
        $this->type = self::ARTICLE;

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        (new TagIndexArticle())->setArticleTags($this->id, $this->tag);
        (new TagIndexImages())->setImages($this->id, $this->images);
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (!empty($this->thumb)) {
            Util::getInstance()->deleteThumbnails(Yii::getAlias('@frontend/web') . $this->thumb, self::$thumbSizes, true);
        }
        Comment::deleteAll(['aid' => $this->id]);
        (new TagIndexArticle())->setArticleTags($this->id, []);

        return parent::beforeDelete();
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $articleMetaImagesModel = new TagIndexImages();
        $this->images = $articleMetaImagesModel->getImagesByArticle($this->id);
        parent::afterFind();
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            if (self::find()->where(['title' => $this->title])->exists()) {
                $this->addError('title', '标题已经存在');
            }
        } else {
            if (self::find()->where(['title' => $this->title])->andWhere(['<>', 'id', $this->id])->exists()) {
                $this->addError('title', '标题已经存在：' . $this->id);
            }
        }
        //为0表示需要删除图片，Util::handleModelSingleFileUpload()会有判断删除图片
        if ($this->thumb !== "0" && substr($this->thumb, 0, 4) !== 'http' && substr($this->thumb, 0, 2) !== '//') {
            $this->thumb = UploadedFile::getInstance($this, "thumb");
        }

        return parent::beforeValidate();
    }

    public function getThumbUrlBySize($width = '', $height = '')
    {
        if (empty($width) || empty($height)) {
            return $this->thumb;
        }
        if (empty($this->thumb)) {//未配图
            $this->thumb = '/static/background/' . rand(1001, 1175) . '.jpg';
        }
        static $str = null;
        if ($str === null) {
            $str = "";
            foreach (self::$thumbSizes as $temp) {
                $str .= $temp['w'] . 'x' . $temp['h'] . '---';
            }
        }
        if (strpos($str, $width . 'x' . $height) !== false) {
            $dotPosition = strrpos($this->thumb, '.');
            $thumbExt = "@" . $width . 'x' . $height;
            if ($dotPosition === false) {
                return $this->thumb . $thumbExt;
            } else {
                return substr_replace($this->thumb, $thumbExt, $dotPosition, 0);
            }
        }

        return Yii::$app->getRequest()->getBaseUrl() . $this->thumb;
    }
}
