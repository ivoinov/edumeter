<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 5/6/13
 * Time: 11:40 AM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Helper_Output
{
    public function getRegionName($value,$input){
        $regionObject = Seven::getModel('iwe_region/entity')->load($value);
        if($regionObject->isLoaded())
            return $regionObject->getName();
        return $value;
    }
}