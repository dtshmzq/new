<?php
// +----------------------------------------------------------------------
// | [  ]
// +----------------------------------------------------------------------
// | f.shuai [ 2022/11/20 12:02 ]
// +----------------------------------------------------------------------
// | Author: f.shuai <1329126822@qq.com>
// +----------------------------------------------------------------------
namespace common\helpers;

use Imagine\Image\Palette\RGB;
use Imagine\Image\Point;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\imagine\Image;

class ImagineHelper extends Image
{
    public static function DownTextWater($image, $fontFile, array $start = [0, 0], array $fontOptions = [])
    {
        if (!isset($start[0], $start[1])) {
            throw new InvalidParamException('$start must be an array of two elements.');
        }

        $fontSize = ArrayHelper::getValue($fontOptions, 'size', 12);
        $fontColor = ArrayHelper::getValue($fontOptions, 'color', 'fff');
        $fontAngle = ArrayHelper::getValue($fontOptions, 'angle', 0);
        $fontAlpha = ArrayHelper::getValue($fontOptions, 'alpha', 100);

        $palette = new RGB();
        $color = $palette->color($fontColor, $fontAlpha);

        $img = self::ensureImageInterfaceInstance($image);
        $font = static::getImagine()->font(\Yii::getAlias($fontFile), $fontSize, $color);
        $img->draw()->text('ixfsh', $font, new Point($start[0], $start[1]), $fontAngle);

        return $img;
    }
}