<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2017-03-15 21:16
 */

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Page;
use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;

class PageSearch extends Page implements SearchInterface
{
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
                    'sort',
                ],
                'integer',
            ],
        ];
    }

    public function behaviors()
    {
        return [
            TimeSearchBehavior::className(),
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
        $query = Page::find()->select([]);
        /** @var $dataProvider ActiveDataProvider */
        $dataProvider = Yii::createObject([
                                              'class' => ActiveDataProvider::className(),
                                              'query' => $query,
                                              'sort'  => [
                                                  'defaultOrder' => [
                                                      'sort' => SORT_ASC,
                                                      'id'   => SORT_DESC,
                                                  ],
                                              ],
                                          ]);
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['like', 'title', $this->title])
              ->andFilterWhere(['status' => $this->status])
              ->andFilterWhere(['sort' => $this->sort])
              ->andFilterWhere(['like', 'seo_keywords', $this->seo_keywords])
              ->andFilterWhere(['like', 'content', $this->content])
              ->andFilterWhere(['like', 'seo_title', $this->seo_title]);
        if ($this->cid === '0') {
            $query->andWhere(['cid' => 0]);
        } else {

        }
        $this->trigger(SearchEvent::BEFORE_SEARCH, Yii::createObject(['class' => SearchEvent::className(), 'query' => $query]));

        return $dataProvider;
    }

}