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
}
