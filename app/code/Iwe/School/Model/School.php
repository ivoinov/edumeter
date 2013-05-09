<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 5/9/13
 * Time: 9:19 AM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Model_School extends Core_Model_Entity
{

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