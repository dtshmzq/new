<?php
/**
 * Author: feiber
 * Created at: 2016-04-02 22:48
 */

namespace frontend\controllers;

use common\models\Comment;
use Yii;
use yii\base\Exception;
use common\helpers\Util;
use common\libs\Constants;
use common\models\Article;
use common\models\ArticleContent;
use common\models\Category;
use linslin\yii2\curl\Curl;

class ApiController extends \yii\web\Controller
{
    public $layout = false;
    public $enableCsrfValidation = false;

    /**
     * single page
     *
     * @param string $name
     * @return string
     * @throws yii\web\NotFoundHttpException
     * @throws yii\base\InvalidConfigException
     */
    public function actionYyzn()
    {
        $token = $this->request->post('token');
        if (empty($token) || $token != 'yyzn_spider_token') {
            exit('密钥异常');
        }
        $title = Util::getInstance()->titleReplace($this->request->post('title'));
        if (empty($title)) {
            exit('标题不能为空');
        }
        if (Article::find()->where(['title' => $title])->exists()) {
            exit("{$title} - 已经存在 ok");
        }
        $content = $this->request->post('content');
        if (empty($content)) {
            exit('内容不能为空');
        }
        $cid = $this->request->post('cid');
        if (empty($cid)) {
            exit('分类不能为空');
        }
        if (!Category::find()->where(['id' => $cid])->exists()) {
            exit("{$cid} - 没找到此分类");
        }
        $keywords = $this->request->post('keywords');
        $status = $this->request->post('status');
        $seoDescription = Util::getInstance()->mSubStr(str_replace(["&nbsp", "\r", "\n", "\r\n"], '', strip_tags($content)), 0, 120);

        $articlePostData = [
            'cid'             => $cid,
            'title'           => $title,
            'seo_keywords'    => $keywords,
            'seo_description' => $seoDescription,
            'status'          => $status,
        ];
        $articleContentPostData = [
            'content' => Util::getInstance()->contentReplace($content),
        ];
        try {
            $transaction = Yii::$app->getDb()->beginTransaction();
            $articleModel = new Article($articlePostData);
            $articleContentModel = new ArticleContent($articleContentPostData);
            if (!$articleModel->save()) {
                throw new Exception('save article error');
            }

            $articleContentModel->setAttribute('aid', $articleModel->id);
            if (!$articleContentModel->save()) {
                throw new Exception('save article content error');
            }
            $transaction->commit();
            echo("{$title} - 发布成功 ok");
        }
        catch (\Exception $exception) {
            $transaction->rollBack();
            echo("create article failed:" . $exception->getFile() . "(" . $exception->getLine() . ")" . $exception->getMessage());
        }
        exit();
    }
}