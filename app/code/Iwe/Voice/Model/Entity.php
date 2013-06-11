<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ilya Voinov
 * User email: ilya.voinov@yahoo.com
 * Date: 6/9/13  
 */
class Iwe_Voice_Model_Entity extends Core_Model_Entity
{
    public function getVoiceType()
    {
        return array(
            1 =>    'Неправильный адрес',
            2 =>    'Неправильное название школы',
            3 =>    'Другая проблема',
        );
    }

    public function getSchoolsName()
    {
        $schoolNames = array();
        $schoolNames = Seven::getCollection('iwe_school/school')
                ->getSelect()
                ->columns(array('id','name'))
                ->fetchPairs();
        return $schoolNames;
    }
}