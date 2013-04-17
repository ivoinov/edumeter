<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 3/17/13
 * Time: 12:58 AM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Controller_Map extends Core_Controller_Crud_Abstract_List
{
    protected function _getDefaultOptions() {
        return array_merge(parent::_getDefaultOptions(), array(
            'grid_init' => 'iwe_school/entity',
            'collection_alias' => 'iwe_school/entity',
        ));
    }

    public function indexAction()
    {
        $this->getLayout()->addTag('school_index');
        $this->_listAbstract();
    }

    public function getschoolAction()
    {
        $result = array();
        $collection = Seven::getCollection('iwe_school/entity');
        foreach($collection as $school)
        {
            $result[] = array(
                'longitude'  => $school->getLongitude(),
                'latitude'   => $school->getLatitude(),
                'rate'       => rand(1 ,10),
                'title'      => $school->getName(),
                'city'       => $school->getCity(),
                'id'         => $school->getId()
            );
        }
        Seven::app()->getResponse()
            ->setIsAjax(true)
            ->setBody(json_encode($result));
                return;

    }
}
