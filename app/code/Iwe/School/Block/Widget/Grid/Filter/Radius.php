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
        if(($currentLongitude) && ($currentLatitude)) {
            foreach($collection as $schoolId => $school) {
                $dist = $this->_calculateDistance($currentLatitude,$currentLongitude,$school->getLatitude(),$school->getLongitude());
                if($dist >= (float)$this->getFilterValue())
                   $school->setId(null);
            }
        }
        return $collection;
    }

    protected function _getCurrentLongitude()
    {
        return Seven::app()->getRequest()->getParam('current_longitude');
    }

    protected function _getCurrentLatitude()
    {
        return Seven::app()->getRequest()->getParam('current_latitude');
    }

    protected function _calculateDistance ($φA, $λA, $φB, $λB) {

        // перевести координаты в радианы
        $lat1 = $φA * M_PI / 180;
        $lat2 = $φB * M_PI / 180;
        $long1 = $λA * M_PI / 180;
        $long2 = $λB * M_PI / 180;

        // косинусы и синусы широт и разницы долгот
        $cl1 = cos($lat1);
        $cl2 = cos($lat2);
        $sl1 = sin($lat1);
        $sl2 = sin($lat2);
        $delta = $long2 - $long1;
        $cdelta = cos($delta);
        $sdelta = sin($delta);

        // вычисления длины большого круга
        $y = sqrt(pow($cl2 * $sdelta, 2) + pow($cl1 * $sl2 - $sl1 * $cl2 * $cdelta, 2));
        $x = $sl1 * $sl2 + $cl1 * $cl2 * $cdelta;

        //
        $ad = atan2($y, $x);
        $dist = $ad * 6372795;

        return $dist;
    }
}

