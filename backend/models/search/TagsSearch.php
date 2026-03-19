<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2016-06-11 22:11
 */

namespace backend\models\search;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\Tags;
use backend\behaviors\TimeSearchBehavior;
use backend\components\search\SearchEvent;

class TagsSearch extends Tags implements SearchInterface
{
    public function behaviors()
    {
        return [
            TimeSearchBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'string'],
            [['id', 'count_num', 'flag_recommend'], 'integer'],
            [['created_at', 'updated_at'], 'string'],
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
        $query = Tags::find()->select([]);
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = Yii::createObject(
            [
                'class' => ActiveDataProvider::className(),
                'query' => $query,
                'sort'  => [
                    'defaultOrder' => [
                        'id' => SORT_DESC,
                    ],
                ],
            ]
        );
        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'value', $this->value])
              ->andFilterWhere(['flag_recommend' => $this->flag_recommend])
              ->andFilterWhere(['like', 'key', $this->key]);

        $this->trigger(SearchEvent::BEFORE_SEARCH, Yii::createObject(['class' => SearchEvent::className(), 'query' => $query]));

        return $dataProvider;
    }
}