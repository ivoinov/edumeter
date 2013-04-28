<?php

class Iwe_School_Model_Entity extends Core_Model_Entity {

    protected $_wayMap = array(
        'math' => array(6,10),
        'arts' => array(1,3,5,9,12,13,14,15,16),
        'natural' => array(2,4,11,7)
    );
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

    public function getRate($way = 'global', $year = 2012)
    {
        $rating = 0;
        if($way === 'global') {
            $ratingsCollection = Seven::getCollection('iwe_ratings/subject_rate')
                ->filter('school_id',$this->getId())
                ->filter('year', $year);
            if(!count($ratingsCollection))
                return -1;
            foreach($ratingsCollection as $rate)
            {
                $rating += $rate->getRate();
            }
            return round($rating / count($ratingsCollection),2);
        }
        if(isset($this->_wayMap[$way]))
            $count = 0;
            foreach($this->_wayMap[$way] as $subjectId) {
                $ratingsCollection = Seven::getCollection('iwe_ratings/subject_rate')
                    ->filter('school_id',$this->getId())
                    ->filter('year', $year)
                    ->filter('subject',$subjectId);
                if(count($ratingsCollection)) {
                    $rating += (float)$ratingsCollection->first()->getRate();
                    $count++;
                }
            }
        return round($rating / $count ,2);
    }
}
