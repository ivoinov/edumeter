<?php

/**
 * Created by PhpStorm.
 * User: b.soroka
 * Date: 2/24/14
 * Time: 1:35 PM
 */
class Iwe_School_Controller_Table extends Iwe_School_Controller_Map
{
    public function indexAction()
    {
        $this->getLayout()->addTag('school_table');
        $this->_listAbstract();
    }

    public function getschoolAction()
    {
        $result = array();

        $region = $this->getRequest()->getParam('region');
        $district = $this->getRequest()->getParam('district');
        $way = $this->getRequest()->getParam('way');
        $year = $this->getRequest()->getParam('year');
        $from = ($this->getRequest()->getParam('from') == 'false') ? 0 : 1;

        $collection = Seven::getCollection('iwe_school/entity')
            ->withTableRate($region, $district, $way, $year, $from);
        foreach ($collection as $school) {
            $rate = round($school->getData('rate'));
            $result[] = array(
                'rate' => $rate,
                'title' => $school->getName(),
                'city' => $school->getCity(),
                'id' => $school->getId(),
            );
        }
        Seven::app()->getResponse()
            ->setIsAjax(true)
            ->setBody(json_encode($result));
        return;

    }
}