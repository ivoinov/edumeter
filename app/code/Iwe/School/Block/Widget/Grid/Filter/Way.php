<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 4/21/13
 * Time: 10:08 PM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Block_Widget_Grid_Filter_Way extends Core_Block_Widget_Grid_Filter_Abstract
{

    public function __construct($data = array())
    {
        if(!isset($data['request_var_name']))
            $data['request_var_name'] = 'way';
        parent::__construct($data);
    }

    public function  apply($collection, $grid)
    {
        return $collection;
    }

}

