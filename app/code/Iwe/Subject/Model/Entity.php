<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 4/22/13
 * Time: 12:24 AM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_Subject_Model_Entity extends Core_Model_Entity
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