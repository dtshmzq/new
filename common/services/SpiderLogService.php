<?php
/**
 * Author: feiber
 * Blog: 
 * Email: 
 * Created at: 2020-01-23 13:50
 */

namespace common\services;

use common\helpers\Util;
use Yii;
use backend\models\search\SpiderLogSearch;
use common\models\SpiderLog;

class SpiderLogService extends Service implements SpiderLogServiceInterface
{

    public function getSearchModel(array $options = [])
    {
        return new SpiderLogSearch();
    }

    public function getModel($id, array $options = [])
    {
        return SpiderLog::findOne($id);
    }

    public function newModel(array $options = [])
    {
        return new SpiderLog();
    }

    public function accessRecord()
    {
        try {
            $userIP = Yii::$app->request->getUserIP();
            $reqUrl = Yii::$app->request->getUrl();
            $userAgent = Yii::$app->request->userAgent;
            if (strpos($reqUrl, 'view-ajax') === false && strpos($reqUrl, 'toastr.js.map') === false && strpos($userAgent, 'Yii2-Curl-Agent') === false) {
                $spiderLog = new SpiderLog();
                $spiderLog->user_agent = $userAgent;
                $spiderLog->url = $reqUrl;
                $spiderLog->ip = $userIP;

                if (Util::getInstance()->checkBaiduSpider($spiderLog->ip) === true) {
                    $spiderLog->spider = (strpos($userAgent, 'Mobile') !== false) ? 'BaiduMobile' : 'Baidu';
                } elseif (Util::getInstance()->checkGoogleSpider($spiderLog->ip) === true) {
                    $spiderLog->spider = (strpos($userAgent, 'Mobile') !== false) ? 'GoogleMobile' : 'Google';
                } elseif (Util::getInstance()->checkBingSpider($spiderLog->ip) === true) {
                    $spiderLog->spider = 'Bing';
                } elseif (Util::getInstance()->checkSogouSpider($spiderLog->ip) === true) {
                    $spiderLog->spider = 'Sogou';
                } elseif (Util::getInstance()->check360spider($spiderLog->ip) === true) {
                    $spiderLog->spider = '360';
                } elseif (Util::getInstance()->checkShenmaSpider($spiderLog->ip) === true) {
                    $spiderLog->spider = 'Shenma';
                } elseif (Util::getInstance()->checkToutiaoSpider($spiderLog->ip) === true) {
                    $spiderLog->spider = 'Toutiao';
                } elseif (strpos($userAgent, 'Yii2-Curl-Agent') !== false) {
                    $spiderLog->spider = 'yii2';
                } else {
//                    $spiderLog->spider = '-';
//                    try {
//                        $whiteIps = explode(PHP_EOL, file_get_contents('/www/wwwroot/white_ip.txt'));
//                        if (in_array($userIP, $whiteIps) || strpos($reqUrl, '.html') === false) {
//                            return true;
//                        }
//                    }
//                    catch (\Exception $ex) {
//                        var_dump($ex->getMessage());
//                    }
                }
                if ($spiderLog->spider == '-') {return true;
                    $spl = SpiderLog::find()->select(['created_at'])->where(['ip' => $userIP])->andWhere(['>=', 'created_at', time() - 72000])
                                    ->orderBy('id desc')->limit(150)->asArray()->all();
                    $slCount = count($spl);
//                    if ($slCount >= 20 || $spl[0]['created_at'] > time()) {
//                        $spiderLog->spider = 'x';
//                        $spiderLog->created_at = time() + $slCount * 60;
//                        Yii::$app->feiber->website_status = SpiderLog::ARTICLE_VIEW;
//                        file_put_contents(
//                            Yii::getAlias("@runtime/blak_ip.txt"),
//                            date('m-d H:i:s') . ' - ' . $userIP . ' - ' . $userAgent . PHP_EOL, FILE_APPEND
//                        );
//                    }
                    if ($slCount < 50) {
                        // $spiderLog->save();
                    }
                } else {
                    // $spiderLog->save();
                }
            }

            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }
}