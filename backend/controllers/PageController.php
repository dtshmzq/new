<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2016-03-23 15:13
 */

namespace backend\controllers;

use Yii;
use common\services\PageServiceInterface;
use backend\actions\CreateAction;
use backend\actions\UpdateAction;
use backend\actions\IndexAction;
use backend\actions\ViewAction;
use backend\actions\DeleteAction;
use backend\actions\SortAction;

/**
 * Page management
 * - data:
 *          table page
 * - description:
 *          frontend single management. please find single page by column `sub_title`
 *
 * Class PageController
 * @package backend\controllers
 */
class PageController extends \yii\web\Controller
{
    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var PageServiceInterface $service */
        $service = Yii::$app->get(PageServiceInterface::ServiceName);

        return [
            'index'      => [
                'class' => IndexAction::className(),
                'data'  => function ($query) use ($service) {
                    $result = $service->getList($query);

                    return [
                        'dataProvider'       => $result['dataProvider'],
                        'searchModel'        => $result['searchModel'],
                        'frontendURLManager' => $service->getFrontendURLManager(),
                    ];
                },
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'data'  => function ($id) use ($service) {
                    return [
                        'model' => $service->getDetail($id),
                    ];
                },
            ],
            'create'     => [
                'class'    => CreateAction::className(),
                'doCreate' => function ($postData) use ($service) {
                    return $service->create($postData);
                },
                'data'     => function ($createResultModel) use ($service) {
                    return [
                        'model' => $createResultModel === null ? $service->newModel() : $createResultModel['pageModel'],
                    ];
                },
            ],
            'update'     => [
                'class'    => UpdateAction::className(),
                'doUpdate' => function ($id, $postData) use ($service) {
                    return $service->update($id, $postData);
                },
                'data'     => function ($id, $updateResultModel) use ($service) {
                    return [
                        'model' => $updateResultModel === null ? $service->getDetail($id) : $updateResultModel['pageModel'],
                    ];
                },
            ],
            'delete'     => [
                'class'    => DeleteAction::className(),
                'doDelete' => function ($id) use ($service) {
                    return $service->delete($id);
                },
            ],
            'sort'       => [
                'class'  => SortAction::className(),
                'doSort' => function ($id, $sort) use ($service) {
                    return $service->sort($id, $sort);
                },
            ],
        ];
    }
}