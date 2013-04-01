<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 3/28/13
 * Time: 9:59 PM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_Ratings_Model_Subject extends Core_Model_Entity
{
    public function getOptionsArray()
    {
        $subjectCollection = $this->getCollection();
        $subjectArray = array();
        foreach($subjectCollection as $subject)
        {
            $subjectArray[$subject->getId()] = $subject->getName();
        }
        return $subjectArray;
    }
}