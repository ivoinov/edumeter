<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 4/11/13
 * Time: 12:30 AM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_Ratings_Model_Entity extends Core_Model_Entity
{
    public function getOptionsArray()
    {
        $schoolCollection = $this->getCollection();
        $options = array();
        foreach($schoolCollection as $school)
        {
            $options[$school->getlId()] = $school->getName();
        }
        return $options;
    }
}
