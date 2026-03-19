<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:31
 */

namespace backend\actions;


use Yii;
use stdClass;
use Closure;
use backend\actions\helpers\Helper;
use yii\base\Exception;
use yii\web\MethodNotAllowedHttpException;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

/**
 * backend update
 * if update occurs error, must return model or error string for display error. return true for successful update.
 * if GET request, the updateResult be a null, POST request the createResult is the value of doUpdate closure returns.
 *
 * Class UpdateAction
 * @package backend\actions
 */
class StateAction extends \yii\base\Action
{

    const UPDATE_REFERER = "_update_referer";

    /**
     * @var string|array primary key(s) name
     */
    public $primaryKeyIdentity = 'id';

    /**
     * @var string primary keys(s) from (GET or POST)
     */
    public $primaryKeyFromMethod = "GET";

    /**
     * @var array|\Closure variables will assigned to view
     */
    public $data;

    /**
     * @var Closure the real update logic, usually will call service layer update method
     */
    public $doState;

    /**
     * @var string after success doUpdate tips message showed in page top
     */
    public $successTipsMessage = "success";


    public function init()
    {
        parent::init();
        if( $this->successTipsMessage === "success"){
            $this->successTipsMessage = Yii::t("app", "success");
        }
    }


    /**
     * update
     *
     * @return array|string
     * @throws UnprocessableEntityHttpException
     * @throws Exception
     */
    public function run()
    {
        //according assigned HTTP Method and param name to get value. will be passed to $this->doUpdate closure and $this->data closure.Often use for get value of primary key.
        $primaryKeys = Helper::getPrimaryKeys($this->primaryKeyIdentity, $this->primaryKeyFromMethod);

        if (Yii::$app->getRequest()->getIsPost()) {//if POST request will execute doUpdate.
            if (!$this->doState instanceof Closure) {
                throw new Exception(__CLASS__ . "::doUpdate must be closure");
            }
            $postData = Yii::$app->getRequest()->post();

            $sateData = [];//doUpdate closure formal parameter(translate: 传递给doUpdate必包的形参)

            if( !empty($primaryKeys) ){
                foreach ($primaryKeys as $primaryKey) {
                    array_push($sateData, $primaryKey);
                }
            }

            array_push($sateData, $postData, $this);

            /**
             * doUpdate(primaryKey1, primaryKey2 ..., $_POST, UpdateAction)
             */
            $result = call_user_func_array($this->doState, $sateData);//call doUpdate closure

            if(  Yii::$app->getRequest()->getIsAjax() ){ //ajax
                Yii::$app->getResponse()->format = Response::FORMAT_JSON;
                if( $result === true ){//only $updateResult is true represent update success
                    return ['code'=>0, 'msg'=>'success', 'data'=>new stdClass()];
                }else{
                    throw new UnprocessableEntityHttpException(Helper::getErrorString($result));
                }
            }else{//not ajax
                if ($result === true) {
                    Yii::$app->getSession()->setFlash('success', $this->successTipsMessage);
                } else {
                    Yii::$app->getSession()->setFlash('error', Helper::getErrorString($result));
                }
                return $this->controller->goBack();
            }

        }else{
            throw new MethodNotAllowedHttpException(Yii::t('app', "must be POST http method"));
        }
    }


}