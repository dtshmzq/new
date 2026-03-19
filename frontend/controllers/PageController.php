<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-04-02 22:48
 */

namespace frontend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\services\PageServiceInterface;

class PageController extends \yii\web\Controller
{
    /**
     * single page
     *
     * @param string $name
     * @return string
     * @throws yii\web\NotFoundHttpException
     * @throws yii\base\InvalidConfigException
     */
    public function actionView($name = '')
    {
        empty($name) && $name = Yii::$app->getRequest()->getPathInfo();

        /** @var PageServiceInterface $service */
        $service = Yii::$app->get(PageServiceInterface::ServiceName);
        $model = $service->getPageSubTitle($name);
        if (empty($model)) {
            throw new NotFoundHttpException('None page named ' . $name);
        }
        $template = "view";
        $model->template != "" && $template = $model->template;

        return $this->render($template, [
            'model'       => $model,
        ]);
    }

}