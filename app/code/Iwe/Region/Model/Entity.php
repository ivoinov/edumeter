<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 4/16/13
 * Time: 8:32 PM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_Region_Model_Entity extends Core_Model_Entity
{
    public function getOptionsArray()
    {
        $regionCollection = $this->getCollection();
        $options = array();
        foreach($regionCollection as $region)
        {
            $options[$region->getId()] = $region->getName();
        }
        return $options;
    }
}
