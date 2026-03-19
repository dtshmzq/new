<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2020-01-30 14:40
 */

namespace common\services;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use common\libs\Constants;
use common\models\Page;
use common\models\meta\TagIndexArticle;
use common\models\TagIndex;
use backend\models\search\PageSearch;

class PageService extends Service implements PageServiceInterface
{
    public function getSearchModel(array $options = [])
    {
        return new PageSearch();
    }

    public function getModel($id, array $options = [])
    {
        return Page::findOne($id);
    }

    public function getDetail($id, array $options = [])
    {
        $model = $this->getModel($id, $options);
        if (empty($model)) {
            throw new NotFoundHttpException("Record " . $id . " not exists");
        }
        $model->tag = (new TagIndexArticle())->getTagsByArticle($model->id, TagIndex::MODULE_PAGE, true);

        return $model;
    }

    public function newModel(array $options = [])
    {
        return new Page();
    }

    public function create(array $postData, array $options = [])
    {
        $pageModel = new Page();
        if (!$pageModel->load($postData)) {
            return [
                'pageModel' => $pageModel,
            ];
        }
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            if (!$pageModel->save()) {
                throw new Exception("save page error");
            }
            $transaction->commit();
        }
        catch (Exception $exception) {
            Yii::error("create page failed:" . $exception->getFile() . "(" . $exception->getLine() . ")" . $exception->getMessage());
            $transaction->rollBack();

            return [
                'pageModel' => $pageModel,
            ];
        }

        return true;
    }

    public function update($id, array $postData, array $options = [])
    {
        /** @var Page $model */
        $model = $this->getDetail($id, $options);

        if (!$model->load($postData) || !$model->validate()) {
            return $model;
        }

        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            if (!$model->save()) {
                throw new Exception("save article error");
            }
            $transaction->commit();
        }
        catch (Exception $exception) {
            $transaction->rollBack();

            return $model;
        }

        return true;
    }

    public function delete($id, array $options = [])
    {
        /** @var Page $pageModel */
        $pageModel = $this->getDetail($id, $options);
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            if ($pageModel->delete() === false) {
                throw new \Exception('delete article failed');
            }
            $transaction->commit();
        }
        catch (Exception $exception) {
            $transaction->rollBack();
            $pageModel->addError('id', $exception->getMessage());

            return $pageModel;
        };

        return true;
    }

    public function getSinglePages()
    {
        return Page::find()->all();
    }

    public function getPageSubTitle($subTitle)
    {
        return Page::findOne(['sub_title' => $subTitle]);
    }

    public function getById($id)
    {
        return Page::find()->where(['id' => $id, 'status' => Constants::YesNo_Yes])->one();
    }

    public function getCountByPeriod($startAt = null, $endAt = null)
    {
        $model = Page::find();
        if ($startAt != null && $endAt != null) {
            $model->andWhere(["between", "created_at", $startAt, $endAt]);
        } elseif ($startAt != null) {
            $model->andwhere([">", "created_at", $startAt]);
        } elseif ($endAt != null) {
            $model->andWhere(["<", "created_at", $endAt]);
        }

        return $model->count('id');
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
        isset($config['components']['urlManager']) && $properties = $config['components']['urlManager'];

        $frontendURLManager = clone Yii::$app->getUrlManager();
        Yii::configure($frontendURLManager, $properties);

        return $frontendURLManager;
    }
}