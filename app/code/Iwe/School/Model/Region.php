<?php

class Iwe_School_Model_Region extends Core_Model_Entity {

    public function getOptionsArray()
    {
        $regionCollection = $this->getCollection();
        $regionArray = array();
        foreach($regionCollection as $region)
        {
            $regionArray[$region->getId()] = $region->getName();
        }
         return $regionArray;
    }
}
