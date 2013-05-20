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
        $way = $this->getRequest()->getParam('way');
        $year = $this->getRequest()->getParam('year');
        $currentLongitude = $this->getRequest()->getParam('longitude');
        $currentLatitude = $this->getRequest()->getParam('latitude');
        $radius = round($this->getRequest()->getParam('viewableRadius'));
        $collection = Seven::getCollection('iwe_school/entity')
            ->getInCurrentRadius($currentLongitude,$currentLatitude,$radius)
            ->withRate($way, $year);
        foreach($collection as $school)
        {
            $rate = round($school->getData('rate'));
            $result[] = array(
                'longitude'  => $school->getLongitude(),
                'latitude'   => $school->getLatitude(),
                'rate'       => $rate,
                'icon'       => $this->_getMarkerIcon($rate, $year),
                'title'      => $school->getName(),
                'city'       => $school->getCity(),
                'id'         => $school->getId(),
                'distance'   => $this->_getFormatDistance($currentLongitude,$currentLatitude,$school->getLongitude(),$school->getLatitude())
            );
        }
        Seven::app()->getResponse()
            ->setIsAjax(true)
            ->setBody(json_encode($result));
                return;

    }

    protected function _getMarkerIcon($rate)
    {
        if($rate === -1)
            return $this->_getSkinUrl('images/pin_red.png');
        if($rate < 150)
            $icon = 'red.png';
        if($rate >= 150 && $rate < 170)
            $icon = 'yellow.png';
        if($rate >= 170)
            $icon = 'green.png';
        return $this->_getSkinUrl('images/'.'pin_'.$icon);
    }

    protected function _getSkinUrl($path)
    {
        $area = $this->getLayout()->getArea();
        $theme = $this->getLayout()->getTheme();
        $url = false;
        foreach (array_reverse(Seven::getConfig('design/skin', $area)) as $assert_path) {
            foreach (array($theme, 'default') as $_theme) {
                $url = "/" . $assert_path . "/" . $_theme . "/" . $path;
                if (is_file(BP . str_replace('/', DS, $url))) {
                    return Seven::app()->getRequest()->getBaseUrl() . $url;
                }
            }
        }
        return Seven::app()->getRequest()->getBaseUrl() . $url;

    }

    protected function _getFormatDistance($φA, $λA, $φB, $λB)
    {
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
        $ad = atan2($y, $x);
        $dist = (int)($ad * 6372795);
        if($dist < 900)
            return round($dist,2) . ' м';
        return round($dist / 1000, 2) . ' км';

    }
}
