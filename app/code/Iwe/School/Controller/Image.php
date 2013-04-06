<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Администратор
 * Date: 06.04.13
 * Time: 3:03
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Controller_Image extends Core_Controller_Abstract
{
    public function indexAction() {
        $max_age = 2592000;
        Seven::app()->getResponse()->setHeader("Cache-Control", "private, max-age = " . $max_age)
            ->setHeader("Pragma", "private")
            ->setHeader("Expires",date('D, d M Y H:i:s', time() + $max_age))
            ->sendHeaders();
        $displayParam = Seven::app()->getRequest()->getParam('infoOnMarker');
        if($displayParam != '?')
            $displayParam = (int) $displayParam;
        Seven::getModel('iwe_school/marker')->renderMarker($displayParam);
    }

}