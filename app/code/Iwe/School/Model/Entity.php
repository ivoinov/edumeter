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

    public function getRate()
    {
        $rating = 0;
        $ratingsCollection = Seven::getCollection('iwe_ratings/subject_rate')
            ->filter('school_id',$this->getId())
            ->filter('year',2012);
        if(!count($ratingsCollection))
            return rand(1,10);
        foreach($ratingsCollection as $rate)
        {
            $rating += $rate->getRate();
        }
        return round($rating / count($ratingsCollection),2);
    }
}
