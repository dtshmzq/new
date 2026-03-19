<?php
/**
 * Author: feiber
 * Blog: 
 * Email:
 * Created at: 2017-03-15 21:16
 */

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;

/**
 * Error controller
 */
class ErrorController extends Controller
{
    /**
     * exception handler
     *
     * @return string
     */
    public function actionIndex()
    {
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $exception = new HttpException(404, Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }
        //if ($exception instanceof Exception) {
        $name = $exception->getName();
        //} else {
        //$name = $this->defaultName ?: Yii::t('Yii', 'Error');
        //}
        if ($code) {
            $name .= " (#$code)";
        }

        //if ($exception instanceof UserException) {
        $message = $exception->getMessage();
        //} else {
        //$message = $this->defaultMessage ?: Yii::t('Yii', 'An internal server error occurred.');
        //}
        $statusCode = $exception->statusCode ? $exception->statusCode : 500;
        if (Yii::$app->getRequest()->getIsAjax()) {
            return "$name: $message";
        } else {
            return $this->render('error', [
                'code'      => $statusCode,
                'name'      => $name,
                'message'   => $message,
                'exception' => $exception,
            ]);
        }
    }
}
