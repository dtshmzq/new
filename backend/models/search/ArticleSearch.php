<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2017-03-15 21:16
 */

namespace backend\models\search;

use Yii;
use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;
use common\models\Article;
use common\models\Category;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ArticleSearch extends Article implements SearchInterface
{

    public $content;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'cid', 'seo_keywords', 'content', 'seo_title'], 'string'],
            [['created_at', 'updated_at'], 'string'],
            [
                [
                    'id',
                    'status',
                    'thumb',
                    'sort',
                ],
                'integer',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className()
        ];
    }

    /**
     * @param array $params
     * @param array $options
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     */
    public function search(array $params = [], array $options = [])
    {
        $query = Article::find()->select([])->where(['type' => $options['type']])->with('category')->joinWith("articleContent");
        /** @var $dataProvider ActiveDataProvider */
        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::className(),
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        $this->load($params);
        if (! $this->validate()) {
            return $dataProvider;
        }
        $query->alias("article")
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['article.id' => $this->id])
            ->andFilterWhere(['status' => $this->status])
            ->andFilterWhere(['sort' => $this->sort])
            ->andFilterWhere(['like', 'seo_keywords', $this->seo_keywords])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'seo_title', $this->seo_title]);
        if ($this->thumb == 1) {
            $query->andWhere(['<>', 'thumb', '']);
        } else {
            if ($this->thumb === '0') {
                $query->andWhere(['thumb' => '']);
            }
        }
        if ($this->cid === '0') {
            $query->andWhere(['cid' => 0]);
        } else {
            if (! empty($this->cid)) {
                $cids = ArrayHelper::getColumn((new Category())->getDescendants($this->cid), 'id');
                if (count($cids) <= 0) {
                    $query->andFilterWhere(['cid' => $this->cid]);
                } else {
                    $cids[] = $this->cid;
                    $query->andFilterWhere(['cid' => $cids]);
                }
            }
        }
        $this->trigger(SearchEvent::BEFORE_SEARCH, Yii::createObject(['class' => SearchEvent::className(), 'query'=>$query]));
        return $dataProvider;
    }

}