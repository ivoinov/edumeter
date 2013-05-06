<?php

class Iwe_School_Model_Entity extends Core_Model_Entity {

    public function getOptionsArray()
    {
        $schoolCollection = $this->getCollection();
        $schoolArray = array();
        foreach($schoolCollection as $school)
        {
            $schoolArray[$school->getId()] = $school->getName() . '&nbsp' . $school->getCity();
        }
         return $schoolArray;
    }

    public function getRate($way = '2', $year = 2012)
    {
        $rateCollection = Seven::getCollection('iwe_way/stat')
                        ->filter('school',$this->getId())
                        ->filter('way',$way)
                        ->filter('year',$year);
        if(count($rateCollection))
            return round($rateCollection->first()->getRate());
        return -1;
    }
}
