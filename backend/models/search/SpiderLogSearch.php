<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-04-01 23:29
 */

namespace backend\models\search;

use Yii;
use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;
use common\models\SpiderLog;
use yii\data\ActiveDataProvider;

class SpiderLogSearch extends SpiderLog implements SearchInterface
{
    public function behaviors()
    {
        return [
            [
                'class'          => TimeSearchBehavior::className(),
                'timeAttributes' => [SpiderLog::tableName() . '.created_at' => 'created_at'],
            ],
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
        $query = SpiderLog::find()->orderBy(["id" => SORT_DESC]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = Yii::createObject([
                                              'class' => ActiveDataProvider::className(),
                                              'query' => $query,
                                          ]);
        $dataProvider->setSort([
                                   'attributes' => [
                                       'id'          => [
                                           'asc'  => ['spider_log.id' => SORT_ASC],
                                           'desc' => ['spider_log.id' => SORT_DESC],
                                       ],
                                       'created_at'  => [
                                           'asc'  => ['created_at' => SORT_ASC],
                                           'desc' => ['created_at' => SORT_DESC],
                                       ],
                                       'url'         => [
                                           'asc'  => ['url' => SORT_ASC],
                                           'desc' => ['url' => SORT_DESC],
                                       ],
                                       'description' => [
                                           'asc'  => ['description' => SORT_ASC],
                                           'desc' => ['description' => SORT_DESC],
                                       ],
                                   ],
                               ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere(['id' => $this->id])
              ->andFilterWhere(['like', 'url', $this->url])
              ->andFilterWhere(['like', 'spider', $this->spider])
              ->andFilterWhere(['like', 'ip', "{$this->ip}%", false])
              ->andFilterWhere(['like', 'user_agent', $this->user_agent]);
        $this->trigger(SearchEvent::BEFORE_SEARCH, Yii::createObject(['class' => SearchEvent::className(), 'query' => $query]));

        return $dataProvider;
    }

}