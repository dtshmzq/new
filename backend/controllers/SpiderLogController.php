<?php
/**
 * Author: feiber
 * Blog:
 * Email:
 * Created at: 2016-04-01 23:26
 */

namespace backend\controllers;

use Yii;
use backend\actions\IndexAction;
use backend\actions\ViewAction;
use backend\actions\DeleteAction;
use common\services\SpiderLogServiceInterface;

/**
 * Class SpiderLogController
 * @package backend\controllers
 */
class SpiderLogController extends \yii\web\Controller
{

    /**
     * @auth
     * - item group=其他 category=日志 description-get=列表 sort=711 method=get
     * - item group=其他 category=日志 description-get=查看 sort=712 method=get  
     * - item group=其他 category=日志 description-post=删除 sort=723 method=post  
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actions()
    {
        /** @var SpiderLogServiceInterface $service */
        $service = Yii::$app->get(SpiderLogServiceInterface::ServiceName);
        return [
            'index' => [
                'class' => IndexAction::className(),
                'data' => function(array $query)use($service){
                    $result = $service->getList($query);
                    return [
                        'dataProvider' => $result['dataProvider'],
                        'searchModel' => $result['searchModel'],
                    ];
                }
            ],
            'view-layer' => [
                'class' => ViewAction::className(),
                'data' => function($id)use($service){
                    return [
                        'model' => $service->getDetail($id),
                    ];
                },
            ],
            'delete' => [
                'class' => DeleteAction::className(),
                'doDelete' => function($id)use($service){
                    return $service->delete($id);
                }
            ],
        ];
    }

}