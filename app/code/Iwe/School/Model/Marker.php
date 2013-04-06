<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Администратор
 * Date: 06.04.13
 * Time: 3:06
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Model_Marker
{
    public function renderMarker($rate) {
        $fontSize = 3;
        $strLength = strlen((int)$rate);
        $filename = BP . '/public/skin/frontend/iwe/images/m'.$strLength.'.png';
        list($width, $height) = getimagesize($filename);
        $image = imagecreatefrompng($filename);
        $image_p = imagecreatetruecolor($width, $height);
        imagealphablending($image_p, false);
        imagesavealpha($image_p,true);
        $transparent = imagecolorallocatealpha($image_p, 255, 255, 255, 127);
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
        $textcolor = imagecolorallocate($image_p, 255, 255, 255);
        $text_width = imagefontwidth($fontSize) * $strLength;
        $text_height = imagefontheight($fontSize);
        $centerX = ceil($width / 2);
        $centerY = ceil($height / 2);
        $devX = $centerX - (ceil($text_width/2)) +1 ;
        $devY = $centerY - (ceil($text_height/2));
        imagestring($image_p, $fontSize, $devX, $devY, $rate, $textcolor);
        header('Content-type: image/png');
        imagepng($image_p);
        imagedestroy($image_p);
    }
}