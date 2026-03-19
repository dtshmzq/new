<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2020-01-30 14:40
 */

namespace common\services;

use common\helpers\Util;
use common\models\meta\TagIndexArticle;
use common\models\TagIndex;
use Yii;
use backend\models\search\ArticleSearch;
use common\libs\Constants;
use common\models\Article;
use common\models\ArticleContent;
use common\models\Comment;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ArticleService extends Service implements ArticleServiceInterface
{

    public function getSearchModel(array $options = [])
    {
        return new ArticleSearch();
    }

    public function getModel($id, array $options = [])
    {
        $model = Article::findOne($id);
        if (isset($options['scenario']) && !empty($options['scenario'])) {
            if ($model !== null) {
                $model->setScenario($options['scenario']);
            }
        }

        return $model;
    }

    public function getDetail($id, array $options = [])
    {
        $model = $this->getModel($id, $options);
        if (empty($model)) {
            throw new NotFoundHttpException("Record " . $id . " not exists");
        }
        $model->tag = (new TagIndexArticle())->getTagsByArticle($model->id, TagIndex::MODULE_ARTICLE, true);

        return $model;
    }

    public function newModel(array $options = [])
    {
        $model = new Article();
        $model->loadDefaultValues();

        return $model;
    }

    public function newArticleContentModel(array $options = [])
    {
        return new ArticleContent();
    }

    public function getArticleContentDetail($id, array $options = [])
    {
        $model = ArticleContent::findOne(['aid' => $id]);
        if (empty($model)) {
            throw new NotFoundHttpException("Id " . $id . " not exists");
        }

        return $model;
    }

    public function create(array $postData, array $options = [])
    {
        $articleModel = new Article();
        $articleContentModel = new ArticleContent();
        $postData['ArticleContent']['content'] = preg_replace(
            "/<img\s*src=(\"|\')(.*?)\\1[^>]*>/is",
            '<img src="$2" title="' . $postData['Article']['title'] . '" alt="' . $postData['Article']['title'] . '"/>',
            $postData['ArticleContent']['content']
        );
        if (!$articleModel->load($postData) || !$articleContentModel->load($postData)) {
            return [
                'articleModel'        => $articleModel,
                'articleContentModel' => $articleContentModel,
            ];
        }
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            if (!$articleModel->save()) {
                throw new Exception("save article error");
            }

            $articleContentModel->setAttribute("aid", $articleModel->id);
            if (!$articleContentModel->save()) {
                throw new Exception("save article content error");
            }
            $transaction->commit();
        }
        catch (Exception $exception) {
            Yii::error("create article failed:" . $exception->getFile() . "(" . $exception->getLine() . ")" . $exception->getMessage());
            $transaction->rollBack();

            return [
                'articleModel'        => $articleModel,
                'articleContentModel' => $articleContentModel,
            ];
        }

        return true;
    }

    public function update($id, array $postData, array $options = [])
    {
        /** @var Article $articleModel */
        $articleModel = $this->getDetail($id, $options);
        /** @var ArticleContent $articleContentModel */
        $articleContentModel = $this->getArticleContentDetail($id);

        $postData['ArticleContent']['content'] = str_replace(['　', '&nbsp;'], ' ', $postData['ArticleContent']['content']);
        $postData['ArticleContent']['content'] = preg_replace("/(\r\n|\n|\r|\t)/i", '', $postData['ArticleContent']['content']);
        if (
            !empty($postData['Article']['automatic_images']) && !empty($postData['Article']['seo_keywords']) &&
            strpos($postData['ArticleContent']['content'], 'img') === false
        ) {
            $postData['ArticleContent']['content'] = Util::getInstance()->autoImages($postData['Article']['seo_keywords'], $postData['ArticleContent']['content']);
        }
        $postData['ArticleContent']['content'] = preg_replace(
            "/<img(?:.*?)src=(\"|\')(.*?)\\1[^>]*>/is",
            '<img src="$2" title="' . $postData['Article']['title'] . '" alt="' . $postData['Article']['title'] . '"/>',
            $postData['ArticleContent']['content']
        );

//        if (empty($postData['Article']['thumb']) && $postData['Article']['thumb'] != '0') {
//            $postData['Article']['thumb'] = str_replace(
//                Yii::$app->params['site']['url'],
//                '',
//                Util::getInstance()->getContentFirstImageUrl($postData['ArticleContent']['content'])
//            );
//        }
        if (isset($postData[$articleModel->formName()]) && !$articleModel->load($postData)) {
            return [
                'articleModel'        => $articleModel,
                'articleContentModel' => $articleContentModel,
            ];
        }

        if (isset($postData[$articleContentModel->formName()]) && !$articleContentModel->load($postData)) {
            return [
                'articleModel'        => $articleModel,
                'articleContentModel' => $articleContentModel,
            ];
        }
        $db = Yii::$app->getDb();
        $transaction = $db->beginTransaction();
        try {
            if (!$articleModel->save()) {
                throw new Exception("save article error");
            }

            if (!$articleContentModel->save()) {
                throw new Exception("save article content error");
            }
            $transaction->commit();
        }
        catch (Exception $exception) {
            $transaction->rollBack();

            return [
                'articleModel'        => $articleModel,
                'articleContentModel' => $articleContentModel,
            ];
        }

        return true;
    }

    public function state($id, array $postData, array $options = [])
    {
        /** @var Article $articleModel */
        $articleModel = $this->getDetail($id, $options);
        if (isset($postData[$articleModel->formName()]) && !$articleModel->load($postData)) {
            return [
                'articleModel' => $articleModel,
            ];
        }
        if (!$articleModel->save()) {
            throw new Exception("save article error");
        }

        return true;
    }

    public function delete($id, array $options = [])
    {
        /** @var Article $articleModel */
        $articleModel = $this->getDetail($id, $options);
        /** @var ArticleContent $articleContentModel */
        $articleContentModel = $this->getArticleContentDetail($id);
        $db = Yii::$app->getDb();
        $transaction = $db->beginTransaction();
        try {
            if ($articleModel->delete() === false || $articleContentModel->delete() === false || !is_int(Comment::deleteAll(['aid' => $id]))) {
                throw new \Exception("delete article failed");
            }
            $transaction->commit();
        }
        catch (Exception $exception) {
            $transaction->rollBack();
            $articleModel->addError("id", $exception->getMessage());

            return $articleModel;
        };

        return true;
    }

    public function getFlagHeadLinesArticles($limit, $sort = SORT_DESC)
    {
        return Article::find()->limit($limit)->with('category')->orderBy(["sort" => $sort])->all();
    }

    public function getArticleById($aid)
    {
        return Article::find()->where(['id' => $aid, "status" => Constants::YesNo_Yes, 'type' => Article::ARTICLE])->one();
    }

    public function getRelatedArticles($aid, $cid, $limit = 20)
    {
        $rows = TagIndex::find()->select(['tid'])->where(['aid' => $aid])->orderBy('created_at asc')->all();
        if (!empty($rows)) {
            $tidxIds = [];
            foreach ($rows as $row) {
                $tidxIds = array_merge($tidxIds, TagIndex::find()->select(['aid'])->where(['tid' => $row['tid']])->andWhere(['<>', 'aid', $aid])->asArray()->column());
            }
            $relatedArrays = array_count_values($tidxIds);
            if (!empty($relatedArrays)) {
                $newRelatedArrs = [];
                if (max($relatedArrays) > 1) {
                    foreach ($relatedArrays as $rak => $relatedArray) {
                        ($relatedArray > 1) && $newRelatedArrs[$rak] = $relatedArray;
                    }
                    arsort($newRelatedArrs);
                }
                $relatedArrays = array_slice(array_merge(array_keys($newRelatedArrs), $tidxIds), 0, 1000, true);
                $relatedIds = implode(',', $relatedArrays);

                $articles = Article::find()
                                   ->with('category')
                                   ->select(['id', 'cid', 'title', 'thumb', 'seo_description'])
                                   ->where(['id' => $relatedArrays, 'type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED])
                                   ->limit($limit)
                                   ->orderBy([new \yii\db\Expression("FIELD (id,{$relatedIds})")])
                                   ->all();
                if (!empty($articles)) {
                    return $articles;
                }
            }
        }

        return Article::find()
                      ->with('category')
                      ->select(['id', 'cid', 'title', 'thumb', 'seo_description'])
                      ->where(['type' => Article::ARTICLE, 'status' => Article::ARTICLE_PUBLISHED, 'cid' => $cid])
                      ->andWhere(['<>', 'id', $aid])
                      ->orderBy('RAND()')->cache(3600)->limit($limit)->all();
    }

    public function getArticlesCountByPeriod($startAt = null, $endAt = null)
    {
        $model = Article::find();
        $model->andWhere(["type" => Article::ARTICLE]);
        if ($startAt != null && $endAt != null) {
            $model->andWhere(["between", "created_at", $startAt, $endAt]);
        } elseif ($startAt != null) {
            $model->andwhere([">", "created_at", $startAt]);
        } elseif ($endAt != null) {
            $model->andWhere(["<", "created_at", $endAt]);
        }

        return $model->count('id');
    }

    public function getArticles2Sidebar(int $limit = 10, $where, $orderBy = 'id desc', $offset = 0)
    {
        return Article::find()->where($where)->orderBy($orderBy)->offset($offset)->limit($limit)->cache(300)->all();
    }

    public function getFrontendURLManager()
    {
        $localConfig = [];
        if (file_exists(Yii::getAlias("@frontend/config/main-local.php"))) {
            $localConfig = require Yii::getAlias("@frontend/config/main-local.php");
        }
        $config = ArrayHelper::merge(
            require Yii::getAlias("@frontend/config/main.php"),
            $localConfig
        );

        $properties = [];
        if (isset($config['components']['urlManager'])) {
            $properties = $config['components']['urlManager'];
        }

        $urlManager = Yii::$app->getUrlManager();
        $frontendURLManager = clone $urlManager;
        Yii::configure($frontendURLManager, $properties);

        return $frontendURLManager;
    }

    public function getSinglePages()
    {
        return Article::find()->where(['type' => Article::SINGLE_PAGE])->all();
    }
}