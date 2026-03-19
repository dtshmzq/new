<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-04-02 22:48
 */

namespace frontend\controllers;

use Yii;
use frontend\controllers\helpers\Helper;
use common\services\ArticleServiceInterface;
use common\models\SpiderLog;
use common\models\Article;
use common\models\Category;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\filters\HttpCache;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\XmlResponseFormatter;

class ArticleController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class'   => VerbFilter::className(),
                'actions' => [
                    'comment' => ['POST'],
                ],
            ],
            [
                'class'        => HttpCache::className(),
                'only'         => ['view'],
                'lastModified' => function ($action, $params) {
                    $id = Yii::$app->getRequest()->get('id');
                    $model = Article::findOne(['id' => $id, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED]);
                    if ($model === null || Yii::$app->feiber->website_status == SpiderLog::ARTICLE_VIEW) {
                        throw new NotFoundHttpException(Yii::t("frontend", "Article id {id} is not exists", ['id' => $id]));
                    }
                    Article::updateAllCounters(['visit_nums' => 1], ['id' => $id]);
                },
            ],
        ];
    }

    /**
     * article list page
     * @param string $cat category name
     * @return string
     * @throws NotFoundHttpException
     * @throws yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $where = ['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED];
        $query = Article::find()->with('category')->where($where);
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => $query,
                'sort'       => [
                    'defaultOrder' => [
                        'sort'       => SORT_ASC,
                        'updated_at' => SORT_DESC,
                        'id'         => SORT_DESC,
                    ],
                ],
                'pagination' => [
                    'defaultPageSize' => 8,
                ],
            ]
        );
        $data = array_merge(
            [
                'dataProvider' => $dataProvider,
                'type'         => '最近更新',
                'seo'          => '',
            ],
            Helper::getCommonInfos()
        );

        return $this->render('index', $data);
    }

    /**
     * list
     * @param string $cat category name
     * @return string
     * @throws NotFoundHttpException
     * @throws yii\base\InvalidConfigException
     */
    public function actionList($cat = '')
    {
        if ($cat == '') {
            $cat = Yii::$app->getRequest()->getPathInfo();
        }
        $where = ['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED];
        if ($cat != '' && $cat != 'index') {
            if ($cat == Yii::t('app', 'UnClassified')) {
                $where['cid'] = 0;
            } else {
                if (!$category = Category::findOne(['alias' => $cat])) {
                    throw new NotFoundHttpException(Yii::t('frontend', 'None category named {name}', ['name' => $cat]));
                }
                $descendants = $category->getDescendants($category['id']);
                if (empty($descendants)) {
                    $where['cid'] = $category['id'];
                } else {
                    $cids = ArrayHelper::getColumn($descendants, 'id');
                    $cids[] = $category['id'];
                    $where['cid'] = $cids;
                }
            }
        }
        $query = Article::find()->with('category')->where($where);
        $dataProvider = new ActiveDataProvider(
            [
                'query'      => $query,
                'sort'       => [
                    'defaultOrder' => [
                        'sort'       => SORT_ASC,
                        'created_at' => SORT_DESC,
                        'id'         => SORT_DESC,
                    ],
                ],
                'pagination' => [
                    'defaultPageSize' => 10,
                ],
            ]
        );
        $template = "list";
        isset($category) && $category->template != "" && $template = $category->template;
        $data = array_merge(
            [
                'dataProvider' => $dataProvider,
                'type'         => $category->name,
                'seo'          => isset($category) ? $category : "",
            ],
            Helper::getCommonInfos()
        );

        return $this->render($template, $data);
    }

    /**
     * article detail page
     * @param $id
     * @return string
     * @throws \yii\web\NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionView($id)
    {
        /** @var ArticleServiceInterface $articleService */
        $articleService = Yii::$app->get(ArticleServiceInterface::ServiceName);
        $model = $articleService->getArticleById($id);
        /** @var Article $model */

        $prev = Article::find()
                       ->select(['id', 'title'])
                       ->where(['cid' => $model->cid, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED])
                       ->andWhere(['>', 'id', $id])
                       ->orderBy("sort asc,id asc")
                       ->limit(1)
                       ->cache(5320000)
                       ->one();
        $next = Article::find()
                       ->select(['id', 'title'])
                       ->where(['cid' => $model->cid, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED])
                       ->andWhere(['<', 'id', $id])
                       ->orderBy("sort asc,id desc")
                       ->limit(1)
                       ->cache(5320000)
                       ->one();//->createCommand()->getRawSql();
        $template = "view";
        isset($model->category) && $model->category->article_template != "" && $template = $model->category->article_template;
        $model->template != "" && $template = $model->template;

        return $this->render(
            $template,
            [
                'model'        => $model,
                'prev'         => $prev,
                'next'         => $next,
            ]
        );
    }

    /**
     * rss
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRss()
    {
        $xml['channel']['title'] = Yii::$app->feiber->website_title;
        $xml['channel']['description'] = Yii::$app->feiber->seo_description;
        $xml['channel']['lin'] = Yii::$app->getUrlManager()->getHostInfo();
        $xml['channel']['generator'] = Yii::$app->getUrlManager()->getHostInfo();
        $models = Article::find()->with('category')->limit(30)->where(['status' => Article::ARTICLE_PUBLISHED])->orderBy('id desc')->all();
        foreach ($models as $model) {
            $xml['channel']['item'][] = [
                'title'       => $model->title,
                'link'        => Url::to(['article/view', 'cat' => $model->category->alias, 'id' => $model->id]),
                'pubData'     => date('Y-m-d H:i:s', $model->created_at),
                'source'      => Yii::$app->feiber->website_title,
                'description' => $model->seo_description,
            ];
        }
        Yii::configure(
            Yii::$app->getResponse(),
            [
                'formatters' => [
                    Response::FORMAT_XML => [
                        'class'    => XmlResponseFormatter::className(),
                        'rootTag'  => 'rss',
                        'version'  => '1.0',
                        'encoding' => 'utf-8',
                    ],
                ],
            ]
        );
        Yii::$app->getResponse()->format = Response::FORMAT_XML;

        return $xml;
    }
}