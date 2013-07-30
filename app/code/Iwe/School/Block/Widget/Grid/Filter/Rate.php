<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 5/2/13
 * Time: 7:11 PM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Block_Widget_Grid_Filter_Rate extends Core_Block_Widget_Grid_Filter_Abstract
{
    protected function _getYearFilterValue()
    {
        return Seven::app()->getRequest()->getParam('year');
    }

    protected function _getWayFilterValue()
    {
        return Seven::app()->getRequest()->getParam('way');
    }

    protected function _getFromFilterValue()
    {
        return Seven::app()->getRequest()->getParam('from');
    }
    public function apply($collection, $grid)
    {
        $way = $this->_getWayFilterValue();
        $year = $this->_getYearFilterValue();
        $from = ($this->_getFromFilterValue() == 'false') ? 0 : 1;
        return $collection->withRate($way, $year, $from);
    }

}