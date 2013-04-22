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
            $rate = $this->_getSchoolRate($school->getId());
            $result[] = array(
                'longitude'  => $school->getLongitude(),
                'latitude'   => $school->getLatitude(),
                'rate'       => $rate,
                'icon'       => $this->_getMarkerIcon($rate),
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

    protected function _getMarkerIcon($rate)
    {
        if($rate >= 1 && $rate <= 5)
            $icon = 'red.png';
        elseif($rate >= 6 && $rate <= 7)
            $icon = 'blue.png';
        else
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

    protected function _getSchoolRate($schoolId)
    {
        $rating = 0;
        $ratingsCollection = Seven::getCollection('iwe_ratings/subject_rate')
            ->filter('school_id',$schoolId)
            ->filter('year',2012);
        if(!count($ratingsCollection))
            return rand(1,10);
        foreach($ratingsCollection as $rate)
        {
            $rating += $rate->getRate();
        }
        return round($rating / count($ratingsCollection),2);
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
