<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-12-27 14:53
 */

namespace common\helpers;

use Yii;
use yii\base\Exception;
use yii\imagine\Image;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use common\components\Singleton;
use common\libs\Constants;
use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use linslin\yii2\curl\Curl;
use QL\QueryList;
use GuzzleHttp\Cookie\CookieJar;

class Util
{

    use Singleton;

    /**
     * 处理单模型单文件上传
     *
     * @param ActiveRecord $model
     * @param              $field
     * @param              $insert
     * @param              $uploadPath
     * @param array        $options
     *                  $options[thumbSizes] array 需要截图的尺寸，如[['w'=>100,'h'=>100]]
     *                  $options['filename'] string 新文件名，默认自动生成
     * @return bool
     * @throws \yii\base\Exception
     */
    public static function handleModelSingleFileUpload(ActiveRecord &$model, $field, $insert, $uploadPath, $options = [])
    {
        $upload = UploadedFile::getInstance($model, $field);
        if ($upload !== null) {
            $uploadPath = Yii::getAlias($uploadPath);
            if (strpos(strrev($uploadPath), '/') !== 0) $uploadPath .= '/';
            if (!FileHelper::createDirectory($uploadPath)) {
                $model->addError($field, "Create directory failed " . $uploadPath);

                return false;
            }
            $fullName = isset($options['filename']) ? $uploadPath . $options['filename'] : $uploadPath . date('YmdHis') . '_' . uniqid() . '.' . $upload->getExtension();
            if (!$upload->saveAs($fullName)) {
                $model->addError($field, Yii::t('app', 'Upload {attribute} error: ' . $upload->error, ['attribute' => Yii::t('app', ucfirst($field))]) . ': ' .
                                         $fullName);

                return false;
            }
            $model->$field = str_replace(Yii::getAlias('@frontend/web'), '', $fullName);
            if (isset($options['thumbSizes'])) self::thumbnails($fullName, $options['thumbSizes']);
            if (!$insert) {
                $file = Yii::getAlias('@frontend/web') . $model->getOldAttribute($field);
                if (file_exists($file) && is_file($file)) unlink($file);
                if (isset($options['thumbSizes'])) self::deleteThumbnails($file, $options['thumbSizes']);
            }
        } else {
            if ($model->$field === '0') {//删除
                $file = Yii::getAlias('@frontend/web') . $model->getOldAttribute($field);
                if (file_exists($file) && is_file($file)) unlink($file);
                if (isset($options['thumbSizes'])) self::deleteThumbnails($file, $options['thumbSizes']);
                $model->$field = '';
            } else {
                if ($insert) {
                    $model->$field = (is_string($model->$field)) ? $model->$field : '';
                } else {
                    $model->$field = empty($model->$field) ? $model->getOldAttribute($field) : $model->$field;
                }

                /*if ($model->cid < 16 && $field == 'thumb' && empty($model->$field) && isset($model->title) && $model->status == Constants::YesNo_Yes) {
                    $uploadPath = Yii::getAlias($uploadPath);
                    if (strpos(strrev($uploadPath), '/') !== 0) $uploadPath .= '/';
                    if (!FileHelper::createDirectory($uploadPath)) {
                        $model->addError($field, "Create directory failed " . $uploadPath);

                        return false;
                    }
                    $fullName = isset($options['filename']) ? $uploadPath . $options['filename'] : $uploadPath . date('YmdHis') . '_' . uniqid() . '.jpg';
                    $model->$field = Util::getInstance()->watermarkImage($model->title, $fullName);
                    if (!empty($model->$field)) {
                        //                        if (isset($options['thumbSizes'])) self::getInstance()->thumbnails($fullName, $options['thumbSizes']);
                        $model->$field = str_replace(Yii::getAlias('@frontend/web'), '', $fullName);
                    }
                }*/
            }
        }
    }

    /**
     * 处理单模型单文件非常态上传
     *
     * @param ActiveRecord $model
     * @param              $field
     * @param              $uploadPath
     * @param              $oldFullName
     * @param array        $options
     * @return bool
     * @throws yii\base\Exception
     */
    public static function handleModelSingleFileUploadAbnormal(ActiveRecord &$model, $field, $uploadPath, $oldFullName, $options = [])
    {
        if (!isset($options['successDeleteOld'])) $options['successDeleteOld'] = true;//成功后删除旧文件
        if (!isset($options['deleteOldFile'])) $options['deleteOldFile'] = false;     //删除旧文件
        $upload = UploadedFile::getInstance($model, $field);
        if ($upload !== null) {
            $uploadPath = Yii::getAlias($uploadPath);
            if (strpos(strrev($uploadPath), '/') !== 0) $uploadPath .= '/';
            if (!FileHelper::createDirectory($uploadPath)) {
                $model->addError($field, "Create directory failed " . $uploadPath);

                return false;
            }
            $fullName = isset($options['filename']) ? $uploadPath . $options['filename'] : $uploadPath . date('YmdHis') . '_' . uniqid() . '.' . $upload->getExtension();
            if (!$upload->saveAs($fullName)) {
                $model->addError($field, Yii::t('app', 'Upload {attribute} error: ' . $upload->error, ['attribute' => yii::t('app', ucfirst($field))]) . ': ' .
                                         $fullName);

                return false;
            }
            $model->$field = str_replace(Yii::getAlias('@frontend/web'), '', $fullName);
            if (isset($options['thumbSizes'])) self::thumbnails($fullName, $options['thumbSizes']);
            if ($options['successDeleteOld'] && $oldFullName) {
                $file = Yii::getAlias('@frontend/web') . $oldFullName;
                if (file_exists($file) && is_file($file)) unlink($file);
                if (isset($options['thumbSizes'])) self::deleteThumbnails($file, $options['thumbSizes']);
            }
        } else {
            if ($model->$field === '0') {//删除
                $file = Yii::getAlias('@frontend/web') . $oldFullName;
                if (file_exists($file) && is_file($file)) unlink($file);
                if (isset($options['thumbSizes'])) self::deleteThumbnails($file, $options['thumbSizes']);
                $model->$field = '';
            } else {
                $model->$field = $oldFullName;
            }
        }
        if ($options['deleteOldFile']) {
            $file = Yii::getAlias('@frontend/web') . $oldFullName;
            if (file_exists($file) && is_file($file)) unlink($file);
            if (isset($options['thumbSizes'])) self::deleteThumbnails($file, $options['thumbSizes']);
        }
    }

    /**
     * 生成各个尺寸的缩略图
     *
     * @param       $fullName string 原图路径
     * @param array $thumbSizes 二维数组 如 [["w"=>110,"height"=>"20"],["w"=>200,"h"=>"30"]]则生成两张缩量图，分别为宽110高20和宽200高30
     * @throws yii\base\InvalidConfigException
     */
    public static function thumbnails($fullName, array $thumbSizes)
    {
        foreach ($thumbSizes as $info) {
            $thumbFullName = self::getThumbName($fullName, $info['w'], $info['h']);
            Image::thumbnail($fullName, $info['w'], $info['h'])->save($thumbFullName);
        }
    }

    /**
     * 删除各个尺寸的缩略图
     *
     * @param $fullName string 原图图片路径
     * @param $thumbSizes array 二维数组 如 [["w"=>110,"height"=>"20"],["w"=>200,"h"=>"30"]]则生成两张缩量图，分别为宽110高20和宽200高30
     * @param $deleteOrigin bool 是否删除原图
     * @throws yii\base\InvalidConfigException
     */
    public static function deleteThumbnails($fullName, array $thumbSizes, $deleteOrigin = false)
    {
        foreach ($thumbSizes as $info) {
            $thumbFullName = self::getThumbName($fullName, $info['w'], $info['h']);
            if (file_exists($thumbFullName) && is_file($thumbFullName)) unlink($thumbFullName);
        }
        if ($deleteOrigin) {
            file_exists($fullName) && unlink($fullName);
        }
    }

    /**
     * 根据原图路径生成缩略图路径
     *
     * @param $fullName string 原图路径
     * @param $width int 长
     * @param $height int 宽
     * @return string 如/path/to/uploads/article/xx@100x20.png
     */
    public static function getThumbName($fullName, $width, $height)
    {
        $dotPosition = strrpos($fullName, '.', mb_strlen(Yii::getAlias('@frontend')));
        $thumbExt = "@" . $width . 'x' . $height;
        if ($dotPosition === false) {
            $thumbFullName = $fullName . $thumbExt;
        } else {
            $thumbFullName = substr_replace($fullName, $thumbExt, $dotPosition, 0);
        }

        return $thumbFullName;
    }

    /**
     * 需要 Imagick高性能图形库
     * @param string $title
     * @param string $image
     * @return string
     * @author: FeiBer <1329126822@qq.com>
     */
    public function watermarkImage(string $title, string $image)
    {
        try {
            $imageI = mt_rand(1, 221);
            $background = Yii::getAlias("@frontend/web/static/background/{$imageI}.jpg");
            $fontPath = Yii::getAlias('@frontend/web/font/msyh.ttc');


            $imagine = new Imagine();
            $imageBG = $imagine->open($background);

            $palette = new RGB();
            $color = $palette->color("000000");

            $font = $imagine->font($fontPath, 24, $color);

            $lines = [];
            $currentLine = null;
            for ($i = 0; $i < mb_strlen($title, 'UTF-8'); $i++) {
                $word = mb_substr($title, $i, 1, 'UTF-8');
                if ($currentLine === null) {
                    $currentLine = $word;
                } else {
                    $testLine = $currentLine . $word;
                    $testbox = $font->box($testLine, 0);
                    if ($testbox->getWidth() <= 415) {
                        $currentLine = $testLine;
                    } else {
                        $lines[] = $currentLine;
                        $currentLine = $word;
                    }
                }
            }
            if ($currentLine !== null) {
                $lines[] = $currentLine;
            }

            $txtCount = count($lines);
            if ($txtCount == 1) {
                $dateBox = $font->box($lines[0]);
                $dateCenterPosition = new Point\Center($dateBox);
                $imageBG->draw()->text(
                    $lines[0],
                    $font,
                    new Point(
                        $imageBG->getSize()->getWidth() / 2 - $dateCenterPosition->getX(),
                        $imageBG->getSize()->getHeight() / 2 - $dateCenterPosition->getY()
                    )
                );
                unset($dateCenterPosition);
            } else {
                $dateBox = $font->box($title);
                switch ($txtCount) {
                    case 2:
                        $positionY = 115;
                        break;
                    case 3:
                        $positionY = 85;
                        break;
                    case 4:
                        $positionY = 70;
                        break;
                    default:
                        $positionY = 15;
                        break;
                }

                foreach ($lines as $key => $value) {
                    $point = new Point(25, ($key == 0 ? $positionY : ($positionY + ($dateBox->getHeight() + 5) * $key)));
                    $imageBG->draw()->text($value, $font, $point, 0);
                    unset($point);
                }
            }

            $imageBG->save($image);
            unset($imageBG, $imagine, $palette, $font);

            return '/' . $image;
        }
        catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();

            return '';
        }
    }

    /**
     * 需要 Imagick高性能图形库
     * @param string $title
     * @param string $image
     * @return string
     * @author: FeiBer <1329126822@qq.com>
     */
    public function watermarkImageShow(string $str)
    {
        $imageCacheKey = $str . 'image';
        $imageI = Yii::$app->cache->get($imageCacheKey);
        if ($imageI === false) {
            $imageI = mt_rand(1, 50);
            Yii::$app->cache->set($imageCacheKey, $imageI, 60 * 60 * 24 * 15);
        }
        $title = Yii::$app->cache->get($str);
        try {
            $background = Yii::getAlias("@frontend/web/static/background/{$imageI}.jpg");
            $fontPath = Yii::getAlias('@frontend/web/font/msyh.ttc');


            $imagine = new Imagine();
            $imageBG = $imagine->open($background);

            $palette = new RGB();
            $color = $palette->color("000000");

            $font = $imagine->font($fontPath, 24, $color);

            $lines = [];
            $currentLine = null;
            for ($i = 0; $i < mb_strlen($title, 'UTF-8'); $i++) {
                $word = mb_substr($title, $i, 1, 'UTF-8');
                if ($currentLine === null) {
                    $currentLine = $word;
                } else {
                    $testLine = $currentLine . $word;
                    $testbox = $font->box($testLine, 0);
                    if ($testbox->getWidth() <= 495) {
                        $currentLine = $testLine;
                    } else {
                        $lines[] = $currentLine;
                        $currentLine = $word;
                    }
                }
            }
            if ($currentLine !== null) {
                $lines[] = $currentLine;
            }

            $txtCount = count($lines);
            if ($txtCount == 1) {
                $dateBox = $font->box($lines[0]);
                $dateCenterPosition = new Point\Center($dateBox);
                $imageBG->draw()->text(
                    $lines[0],
                    $font,
                    new Point(
                        $imageBG->getSize()->getWidth() / 2 - $dateCenterPosition->getX(),
                        $imageBG->getSize()->getHeight() / 2 - $dateCenterPosition->getY()
                    )
                );
                unset($dateCenterPosition);
            } else {
                $dateBox = $font->box($title);
                switch ($txtCount) {
                    case 2:
                        $positionY = 35;
                        break;
                    default:
                        $positionY = 15;
                        break;
                }

                foreach ($lines as $key => $value) {
                    $point = new Point(25, ($key == 0 ? $positionY : ($positionY + ($dateBox->getHeight() + 5) * $key)));
                    $imageBG->draw()->text($value, $font, $point, 0);
                    unset($point);
                }
            }

            header("Content-type: image/png");

            return $imageBG->show('png');
        }
        catch (\Exception $e) {
            return '';
        }
    }

    public static function getViewTemplate($type = "article")
    {
        if ($type == "article") {
            $files = Yii::$app->params['article.template.directory'];
        } else if ($type == "page") {
            $files = Yii::$app->params['page.template.directory'];
        } else if ($type == "category") {
            $files = Yii::$app->params['category.template.directory'];
        } else {
            throw new Exception("Unknown " . $type);
        }
        $templates = [];
        foreach ($files as $key => $file) {
            if (!is_int($key)) {
                $templates[str_replace(Yii::getAlias("@frontend/views"), "", $key)] = $file;
            } else {
                $templates[str_replace(Yii::getAlias("@frontend/views"), "", $file)] = $file;
            }
        }

        return $templates;
    }

    /**
     * 字符串截取','支持中文和其他编码
     * @access public
     * @param string  $str 需要转换的字符串
     * @param integer $start 开始位置
     * @param string  $length 截取长度
     * @param string  $charset 编码格式
     * @param bool    $suffix 截断显示字符
     * @return string
     */
    public static function mSubStr(string $str, int $start = 0, int $length, string $charset = 'utf-8', bool $suffix = false): string
    {
        if (function_exists("mb_substr")) {
            $slice = mb_substr($str, $start, $length, $charset);
        } elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }

        return $suffix ? $slice . '...' : $slice;
    }

    public static function getContentFirstImageUrl(string $content)
    {
        if (empty($content)) {
            return '';
        }
        preg_match_all('/<img.*src="(.*)"/isU', $content, $matches);

        return isset($matches[1][0]) ? $matches[1][0] : '';
    }

    public static function mobileContentImageUrl(string $content)
    {
        if (empty($content)) {
            return '';
        }
        preg_match_all('/<img.*src="(.*)"/isU', $content, $matches);
        if (isset($matches[1]) && !empty($matches[1])) {
            $matches[1] = array_unique($matches[1]);
            foreach ($matches[1] as $val) {
                if (strpos('http', strtolower($val)) === false) {
                    $content = str_replace($val, Yii::$app->feiber->website_url . $val, $content);
                }
            }
        }

        return $content;
    }

    public function wrapText(string $srt, int $len)
    {
        $arrKey = $enKey = 0;
        $strArr = [];
        $currentNum = 1;
        $srt = str_replace(' ', '', $srt);
        $srt = str_replace('，', ',', $srt);
        $srt = str_replace('：', ':', $srt);
        $srt = str_replace('！', '!', $srt);
        $srt = str_replace('？', '?', $srt);
        $srt = str_replace('（', '(', $srt);
        $srt = str_replace(' ）', ')', $srt);
        for ($i = 0; $i < mb_strlen($srt, 'UTF-8'); $i++) {
            $word = mb_substr($srt, $i, 1, 'UTF-8');
            $flag = preg_match("/[\x7f-\xff]/", $word);
            //如果是英文，第一次循环，进入下一个循环
            if (empty($flag) && $enKey == 0) {
                $enKey++;
                $strArr[$arrKey] .= $word;
                continue;
            }
            $enKey = 0;
            $strArr[$arrKey] .= $word;
            if ($currentNum % $len == 0) {
                $arrKey++;
            }
            $currentNum++;
        }

        return $strArr;
    }


    public function getHeadersExtension($url)
    {
        $mimes = [
            'image/bmp'    => 'bmp',
            'image/gif'    => 'gif',
            'image/jpeg'   => 'jpg',
            'image/png'    => 'png',
            'image/x-icon' => 'ico',
            'image/webp'   => 'webp',
        ];
        if (($headers = get_headers($url, 1)) !== false) {
            // 获取响应的类型
            $imgType = $headers['Content-Type'];
            $extension = $mimes[$imgType];

            return $extension;
        }

        return false;
    }

    public function getImgWater(string $url, $path = '', $crop = true)
    {
        try {
            $fileSuffix = strtolower(pathinfo($url, PATHINFO_EXTENSION));
            $suffix = ['gif', 'jpg', 'png'];
            $unique = substr(md5($url), 8, 16);
            if (!in_array($fileSuffix, $suffix)) {
                $filename = $unique . '.jpg';
            } else {
                $filename = $unique . '.' . $fileSuffix;
            }
            $uploadPath = Yii::getAlias('@frontend/web/uploads/') . $path;
            $rtnImgPath = "/uploads/{$path}{$filename}";
            $localSaveImgPath = $uploadPath . $filename;
            // 判断本地是否存在改文件
            if (file_exists($localSaveImgPath)) {

                return $rtnImgPath;
            }
            if (!FileHelper::createDirectory($uploadPath)) {
                return false;
            }

            // 若不存在则获得远程图片
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            $imagedata = curl_exec($curl);
            curl_close($curl);
            // 保存远程图片到服务器
            $tp = @fopen($localSaveImgPath, 'a');
            if ($tp === false) {
                return false;
            }
            fwrite($tp, $imagedata);
            fclose($tp);
            if ($fileSuffix === 'gif') {
                return $rtnImgPath;
            }
            // 裁剪图片
            $re = getimagesize($localSaveImgPath);
            if (empty($re[0])) {
                @unlink($localSaveImgPath);

                return false;
            } elseif ($re['0'] > 620) {
                // 获得图片的大小
                ImagineHelper::thumbnail($localSaveImgPath, 620, null)->save();
                $re = getimagesize($localSaveImgPath);
            }
            if ($crop === true && $re[1] >= 120) {
                if (strpos($url, 'officezhushou.com') !== false || strpos($url, 'cmcmcdn.com') !== false || strpos($url, 'zol-img.com.cn') !== false) {
                    $cropY = 5;
                } elseif (strpos($url, 'douyinpic.com') !== false) {
                    $cropY = 26;
                }  elseif (strpos($url, 'heilanggou.com') !== false) {
                    $cropY = 53;
                } else {
                    $cropY = 28;
                }
                ImagineHelper::crop($localSaveImgPath, $re['0'], $re['1'] - $cropY, [8, 0])->save();
                $re = getimagesize($localSaveImgPath);
            }
            // 文字水印
//            $fontPath = Yii::getAlias('@frontend/web/static/font/msyh.ttc');
//            $pointXStart = ceil(($re['0'] - 120) / 2);
//            if ($pointXStart < 0) {
//                $pointXStart = $re['0'] > 130 ? $re['0'] - 210 : 0;
//            }
//            $pointYStart = ceil(($re['1'] - 70) / 2);
//            if ($pointYStart < 0) {
//                $pointYStart = $re['1'] > 110 ? $re['1'] - 80 : 0;
//            }
//            ImagineHelper::DownTextWater($localSaveImgPath, $fontPath, [$pointXStart, $pointYStart], ['color' => '000000', 'size' => 38, 'alpha' => 7])->save();

            // 返回压缩后的图片
            return $rtnImgPath;
        }
        catch (\Exception $e) {
            var_dump($e->getMessage());

            return false;
        }
    }

    public function texsmart(string $keywords)
    {
        //分词
        $url = 'https://texsmart.qq.com/api';
        $curl = new Curl();
        $response = $curl->reset()
                         ->setOption(CURLOPT_POSTFIELDS, json_encode(['str' => $keywords]))
                         ->setOption(CURLOPT_RETURNTRANSFER, true)
                         ->setOption(CURLOPT_HTTPHEADER, ['Content-Type: text/json'])
                         ->post($url);
        $response = json_decode($response, true);
        if (isset($response['word_list']) && !empty($response['word_list'])) {
            foreach ($response['word_list'] as $val) {
                if (isset($val['tag']) && $val['tag'] == 'NN' && strlen($val['str']) > 3) {
                    $tags[] = $val['str'];
                }
            }
        }
        if (isset($response['phrase_list']) && !empty($response['phrase_list'])) {
            foreach ($response['phrase_list'] as $val) {
                if (isset($val['tag']) && $val['tag'] == 'NN' && strlen($val['str']) > 3) {
                    $tags[] = $val['str'];
                }
            }
        }

        return isset($tags) ? implode(',', array_unique($tags)) : '';
    }

    public function baiduDropdownWord(string $string)
    {
        $string = urlencode($string);
        try {
            return file_get_contents('https://www.baidu.com/sugrec?ie=utf-8&json=1&prod=pc&wd=' . $string);
        }
        catch (\Exception $e) {
            return "";
        }
    }

    public function getBingImg(string $keywords)
    {
        $url = 'https://cn.bing.com/images/search?q=' . $keywords . '&go=%E6%90%9C%E7%B4%A2&qs=ds&form=QBIR&first=1&tsc=ImageHoverTitle';
        try {
            $ql = QueryList::getInstance()->get($url, null);
            $onlineHrefs = $ql->rules(
                [
                    'url'   => ['a.iusc', 'm'],
                    'title' => ['a.iusc', 'text'],
                ]
            )->range('div.imgpt')->query()->getData()->all();
        }
        catch (\Exception $e) {
            return false;
        }

        if (empty($onlineHrefs)) {
            return false;
        }
        $imgList = [];
        $imgListCount = count($onlineHrefs);
        ($imgListCount > 15) && $imgListCount = 15;
        foreach ($onlineHrefs as $k => $v) {
            if ($k >= $imgListCount) break;
            $urlJson = json_decode($v['url'], true);
            $imgList[] = $urlJson['turl'];
        }
        $randKey = array_rand($imgList, 3);

        return [$imgList[$randKey[0]], $imgList[$randKey[1]], $imgList[$randKey[2]]];
    }

    function autoImages($title, $html)
    {
        $bingImg = self::getInstance()->getBingImg($title);
        if ($bingImg === false || !is_array($bingImg) || count($bingImg) < 3) {
            return $html;
        }
        $pattern = "/(<h2.*?>.*?<\/h2>)/ims";
        preg_match_all($pattern, $html, $match);
        if (isset($match[1]) && !empty($match[1])) {
            foreach ($match[1] as $mk => $mv) {
                if ($mk > 2) break;
                if ($bingImg !== false) {
                    $html = str_replace($mv, $mv . '<p style="text-align: center"><img src="' . $bingImg[$mk] . '" alt="' . $title . '"></p>', $html);
                }
            }
        } else {
            $html = '<p style="text-align: center"><img src="' . $bingImg[0] . '" alt="' . $title . '"></p>' . $html;
        }

        return $html;
    }

    public function getToutiaoTwid()
    {
        $data = [
            "aid"       => 2176,
            "service"   => "so.toutiao.com",
            "unionHost" => "https://ttwid.bytedance.com",
            "union"     => true,
            "needFid"   => false,
        ];

        $jar = new CookieJar();
        QueryList::getInstance()->postJson('https://ttwid.bytedance.com/ttwid/union/register/', $data, [
            'timeout'      => 5,
            'User-Agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:99.0) Gecko/20100101 Firefox/99.0',
            'Referer'      => 'https://so.toutiao.com/',
            'Content-Type' => 'application/json',
            'cookies'      => $jar,
        ]);
        $cookieArr = $jar->toArray();

        return $cookieArr;
    }

    public function getToutiao(string $keywords, int $limit = 3)
    {
        $keywords = urlencode($keywords);

        try {
            $details = [];
            for ($i = 0; $i <= 5; $i++) {
                $url = "https://so.toutiao.com/search?keyword={$keywords}&pd=question&page_num={$i}&from=question&cur_tab_title=question&aid=2176";

                $jar2 = new CookieJar(false, self::getInstance()->getToutiaoTwid());
                $ql = QueryList::getInstance()->get($url, null, [
                    'timeout'    => 5,
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:99.0) Gecko/20100101 Firefox/99.0',
                    'Referer'    => 'https://so.toutiao.com/',
                    'cookies'    => $jar2,
                ]);

                $items = $ql->find('script[type="application/json"]')->texts();
                if (!empty($items)) {
                    foreach ($items as $ik => $item) {
                        $itemArr = json_decode($item, true);
                        if (!isset($itemArr['source_url'])) {
                            continue;
                        }
                        if (isset($itemArr['display']['self_info']['answer_list']) && count($itemArr['display']['self_info']['answer_list']) > 0) {
                            $details[$ik]['title'] = $itemArr['display']['title']['text'];
                            $details[$ik]['url'] = "https://so.toutiao.com/s/search_wenda_pc/list?enter_answer_id={$itemArr['display']['self_info']['selected_ansid']}&enter_from=search_result&qid={$itemArr['display']['self_info']['group_id']}";
                        }
                    }
                }
                if (count($details) >= $limit) break;
            }

            $spiderCount = 0;
            foreach ($details as $dk => $detail) {
                sleep(1);
                $ql = QueryList::getInstance()->get($detail['url'], null, [
                    'timeout'    => 5,
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:99.0) Gecko/20100101 Firefox/99.0',
                    'Referer'    => 'https://www.toutiao.com/',
                    'cookies'    => $jar2,
                ]);
                $answers = $ql->find('script[type="application/json"]:eq(1)')->html();
                $answers = json_decode($answers, true);
                if (!isset($answers['data']['question_details']['data']) || empty($answers['data']['question_details']['data'])) {
                    unset($details[$dk]);
                    continue;
                }
                $answers = $answers['data']['question_details']['data'];
                $answers = isset($answers['answers']) ? $answers['answers'] : [];

                $content = '';
                foreach ($answers as $answer) {
                    $content .= $answer['content'];
                    if (mb_strlen(strip_tags($content)) > 50) {
                        break;
                    }
                }
                if (mb_strlen(strip_tags($content)) <= 15) {
                    unset($details[$dk]);
                    continue;
                }
                $details[$dk]['content'] = Util::getInstance()->contentReplace2Toutiao($content);
                $spiderCount++;
                if ($spiderCount > $limit) break;
            }
            if (empty($details)) return false;

            return array_splice($details, 0, $limit);
        }
        catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        return false;
    }

    public function mbRtrim($string, $trim, $encoding)
    {
        $mask = [];
        $trimLength = mb_strlen($trim, $encoding);
        for ($i = 0; $i < $trimLength; $i++) {
            $item = mb_substr($trim, $i, 1, $encoding);
            $mask[] = $item;
        }

        $len = mb_strlen($string, $encoding);
        if ($len > 0) {
            $i = $len - 1;
            do {
                $item = mb_substr($string, $i, 1, $encoding);
                if (in_array($item, $mask)) {
                    $len--;
                } else {
                    break;
                }
            }
            while ($i-- != 0);
        }

        return mb_substr($string, 0, $len, $encoding);
    }

    public function titleReplace(string $title)
    {
        mb_internal_encoding("UTF-8");
        $encoding = mb_internal_encoding();
        $title = str_replace(['（', '）', '【', '】', '、', '：', '，'], ['(', ')', '[', ']', '/', ':', ','], $title);
        $title = self::getInstance()->mbRtrim(str_replace(["？", "?", "，", '。', '!', '！'], ',', $title), ',', $encoding);
        $title = str_replace([',(', '(,', ',)', '),'], ['(', '(', ')', ')'], $title);

        return trim($title);
    }

    public function contentReplace(string $str)
    {
        $str = str_replace('　', '  ', $str);
        $str = preg_replace('/<style>(.*?)<\/style>/is', '', $str);
        $str = preg_replace('/<p data-track="(.*?)">/', '<p>', $str);
        $str = preg_replace('/<h2[^>]*>(.*?)<\/h2>/is', '<h2>$1</h2>', $str);
        $str = preg_replace('/<h3[^>]*>(.*?)<\/h3>/is', '<h3>$1</h3>', $str);
        $str = preg_replace('/<tt-audio[^>]*>(.*?)<\/tt-audio>/is', '', $str);
        $str = preg_replace('/<p>\s*/', '<p>', $str);
        $str = preg_replace("/<br.*?>/i", "<br/>", $str);
        $str = preg_replace('/<br\/>\s*/', '<br/>', $str);
        $str = preg_replace('/\s*<br\/>/', '<br/>', $str);
        //        $str = preg_replace("/(?:[？|?]\s*){2,}/i", '？', $str);
        //        $str = preg_replace("/(?:<br\/>\s*){2,}/i", '<br/>', $str);
        $str = str_replace(['<br/><br/><br/><br/>', '<br/><br/><br/>', '<br/><br/>',], '<br/>', $str);
        $str = str_replace(['。。。。', '。。。', '。。',], '。', $str);
        $str = str_replace(['！！！！', '！！！', '！！',], '！', $str);
        $str = str_replace(['？，',], '？', $str);
        $str = str_replace(['.，',], '，', $str);
        $str = str_replace(['<p><p>', '<p><br/>', '<p style="white-space: normal;">', '<p>&nbsp;&nbsp;', '<p>&nbsp;&nbsp;&nbsp;', '<p>&nbsp;'], '<p>', $str);
        $str = str_replace(['</p></p>', '<br/></p>',], '</p>', $str);
        $str = str_replace(['？</h2>', '?</h2>', '。</h2>', '.</h2>', '</h2>',], '</h2>', $str);
        $str = preg_replace('/<p>\s*<\/p>/', '', $str);
        $str = preg_replace('/<\s*img[\s\S]+?(?:src=[\'"]([\S\s]*?)[\'"]\s*|alt=[\'"]([\S\s]*?)[\'"]\s*|[a-z]+=[\'"][\S\s]*?[\'"]\s*)+[\s\S]*?>/i',
                            '<img src="$1" alt="$2" title="$2" />',
                            $str);

        return $str;
    }

    public function contentReplace2Toutiao(string $str)
    {
        $str = strip_tags($str, '<p><img><h2><h3><h4><br>');
        $str = str_replace(['　', '&nbsp;'], ' ', $str);
        $str = preg_replace("/(\r\n|\n|\r|\t)/i", '', $str);
        $str = preg_replace("/<p.*?>/i", "<p>", $str);
        $str = preg_replace('/<p>\s*/', '<p>', $str);
        $str = preg_replace("/<br.*?>/i", "<br/>", $str);
        $str = str_replace(['<br/><br/><br/><br/>', '<br/><br/><br/>', '<br/><br/>',], '<br/>', $str);
        $str = str_replace(['。。。。', '。。。', '。。',], '。', $str);
        $str = str_replace(['！！！！', '！！！', '！！',], '！', $str);
        $str = str_replace(['？，', '？？',], '？', $str);
        $str = str_replace(['<p><p>', '<p><br/>'], '<p>', $str);
        $str = str_replace(['<br/></p>',], '</p>', $str);
        $str = str_replace(
            [
                '感谢邀请，', '感谢邀请！', '你好，谢谢邀请！', '谢谢邀请！', '谢谢邀请。', '谢谢邀请：', '谢邀请，', '谢邀请！', '谢邀请；', '谢邀！', '谢邀，', '谢谢浏览。',
                '條萊垍頭', '頭條萊垍', '萊垍頭條', '垍頭條萊', '￼',
                '展开全部', '[捂脸]', '回答完毕', '很高兴为您解答', '请点击输入图片描述', '如图所示。', '你好！', '您好，', '你好，',
                '<p><br/></p>', '<p>&nbsp;</p>', '<p>！</p>', '<p>;</p>', '<h2><br/></h2>', '<blockquote></blockquote>',
            ], '', $str);
        $str = preg_replace('/<p>\s*<\/p>/', '', $str);
        $str = preg_replace('/<\s*img[\s\S]+?(?:src=[\'"]([\S\s]*?)[\'"]\s*|alt=[\'"]([\S\s]*?)[\'"]\s*|[a-z]+=[\'"][\S\s]*?[\'"]\s*)+[\s\S]*?>/i',
                            '<img src="$1" alt="$2" title="$2" />',
                            $str);

        return $str;
    }

    public function check360spider(string $ip)
    {
        $spiderIps = [
            '180.153.232.', '180.153.234.', '180.153.236.', '180.163.220.', '42.236.101.', '42.236.102.', '42.236.103.', '42.236.10.', '42.236.12.',
            '42.236.13.', '42.236.14.', '42.236.15.', '42.236.16.', '42.236.17.', '42.236.46.', '42.236.48.', '42.236.49.',
            '42.236.50.', '42.236.51.', '42.236.52.', '42.236.53.', '42.236.54.', '42.236.55.', '42.236.99.','220.181.132.235','182.118.31.216',
        ];
        foreach ($spiderIps as $spiderIp) {
            if (strpos($ip, $spiderIp) !== false) {
                return true;
            }
        }

        return false;
    }

    public function checkBaiduSpider(string $ip)
    {
        $spiderIps = ['113.24.224.', '116.179.', '220.181.', '61.146.178.'];
        foreach ($spiderIps as $spiderIp) {
            if (strpos($ip, $spiderIp) !== false) {
                return true;
            }
        }

        return false;
    }

    public function checkBingSpider(string $ip)
    {
        $spiderIps = ['20.15.133.', '207.46.13.', '40.77.167.', '40.77.202.', '40.77.95.', '52.167.144.','157.55.39.'];
        foreach ($spiderIps as $spiderIp) {
            if (strpos($ip, $spiderIp) !== false) {
                return true;
            }
        }

        return false;
    }

    public function checkShenmaSpider(string $ip)
    {
        $spiderIps = ['101.67.29.', '101.67.49.', '101.67.50.', '112.13.112.', '124.160.170.', '39.173.105.', '39.173.107.', '60.188.10.', '60.188.11.', '60.188.9.'];
        foreach ($spiderIps as $spiderIp) {
            if (strpos($ip, $spiderIp) !== false) {
                return true;
            }
        }

        return false;
    }

    public function checkSogouSpider(string $ip)
    {
        $spiderIps = [
            '61.135.159.', '58.250.125.', '49.7.21.', '49.7.20.', '43.231.99.', '123.183.224.', '123.125.109.', '123.126.68.', '123.126.50.',
            '111.202.101.', '118.184.177.',
        ];
        foreach ($spiderIps as $spiderIp) {
            if (strpos($ip, $spiderIp) !== false) {
                return true;
            }
        }

        return false;
    }

    public function checkGoogleSpider(string $ip)
    {
        $spiderIps = ['66.249.66.', '66.249.68.', '66.249.69.', '66.249.70.', '66.249.71.', '66.249.72.', '66.249.73.', '66.249.75.', '66.249.79.'];
        foreach ($spiderIps as $spiderIp) {
            if (strpos($ip, $spiderIp) !== false) {
                return true;
            }
        }

        return false;
    }

    public function checkToutiaoSpider(string $ip)
    {
        $spiderIps = ['111.225.149.', '111.225.148.', '110.249.202.', '110.249.201.'];
        foreach ($spiderIps as $spiderIp) {
            if (strpos($ip, $spiderIp) !== false) {
                return true;
            }
        }

        return false;
    }
}
