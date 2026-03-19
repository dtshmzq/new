<?php
/**
 * Author: feiber
 * Blog:
 * Email: 
 * Created at: 2016-04-05 13:08
 */

namespace frontend\controllers;

use common\models\TagIndex;
use common\models\Tags;
use Yii;
use frontend\controllers\helpers\Helper;
use common\models\meta\TagIndexArticle;
use common\models\Article;
use yii\helpers\Html;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{

    /**
     * search
     *
     * @return string
     */
    public function actionIndex()
    {
        $where = ['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED];
        $query = Article::find()->select([])->where($where);
        $keyword = Yii::$app->getRequest()->get('q');
        $query->andFilterWhere(['like', 'title', $keyword]);
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => $query,
                'sort'       => [
                    'defaultOrder' => [
                        'sort' => SORT_ASC,
                        'id'   => SORT_DESC,
                    ],
                ],
                'pagination' => [
                    'defaultPageSize' => 12,
                ],
            ]
        );

        $data = array_merge(
            [
                'dataProvider' => $dataProvider,
                'type'         => Yii::t('frontend', 'Search keyword {keyword} results', ['keyword' => Html::encode($keyword)]),
            ], Helper::getCommonInfos());

        return $this->render('/article/search', $data);
    }

    public function actionTag($id = '')
    {
        $tagName = Tags::find()->select(['value'])->where(['id' => $id])->scalar();
        $where = ['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED];

        $metaTagModel = new TagIndexArticle();
        $aids = $metaTagModel->getAidsByTag($id, TagIndex::MODULE_ARTICLE);
        if (empty($aids)) {
            $query = Article::find()->where($where);
        } else {
            $query = Article::find()->where($where)->andWhere(['in', 'id', $aids]);
        }
        if(empty($query->count())){
            $query = Article::find()->where($where)->orderBy('RAND()');
            $dataProvider = new ActiveDataProvider(
                [
                    'query'      => $query,
                    'pagination' => [
                        'defaultPageSize' => 12,
                    ],
                ]
            );
        }else{
            $dataProvider = new ActiveDataProvider(
                [
                    'query'      => $query,
                    'sort'       => [
                        'defaultOrder' => [
                            'sort' => SORT_ASC,
                            'id'   => SORT_DESC,
                        ],
                    ],
                    'pagination' => [
                        'defaultPageSize' => 12,
                    ],
                ]
            );
        }

        if (isset($aids) && empty($aids)) {
            $dataProvider->setTotalCount(360);
        }

        $seo = new \stdClass();
        $seo->seo_title = Yii::t('frontend', 'Tag {tag} related articles', ['tag' => Html::encode($tagName)]);
        $seo->seo_keywords = Html::encode($tagName);
        $seo->seo_description = "本页主要是关于[{$seo->seo_keywords}]相关的信息聚合整理，对于[{$seo->seo_keywords}]类的信息进行专题汇总收集，便于查阅关于[{$seo->seo_keywords}]有关的所有文章信息。";
        $data = array_merge(
            [
                'dataProvider' => $dataProvider,
                'type'         => Yii::t('frontend', 'Tag {tag} related articles', ['tag' => Html::encode($tagName)]),
                'seo'          => $seo,
            ], Helper::getCommonInfos()
        );

        return $this->render('/article/search', $data);
    }
}
