<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 4/21/13
 * Time: 10:08 PM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Block_Widget_Grid_Filter_Radius extends Core_Block_Widget_Grid_Filter_Abstract
{

    public function __construct($data = array())
    {
        if(!isset($data['request_var_name']))
            $data['request_var_name'] = 'radius';
        parent::__construct($data);
    }

    public function  apply($collection, $grid)
    {
        $currentLongitude = (float) $this->_getCurrentLongitude();
        $currentLatitude = (float) $this->_getCurrentLatitude();
        $radius = (float)$this->getFilterValue();
        return $collection->getInCurrentRadius($currentLongitude,$currentLatitude,$radius);
    }

    protected function _getCurrentLongitude()
    {
        return Seven::app()->getRequest()->getParam('current_longitude');
    }

    protected function _getCurrentLatitude()
    {
        return Seven::app()->getRequest()->getParam('current_latitude');
    }
}

