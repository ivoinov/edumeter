<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Администратор
 * Date: 02.04.13
 * Time: 23:06
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Controller_Admin_Crud extends Core_Controller_Crud
{
    protected function _getDefaultOptions() {
        return array_merge(parent::_getDefaultOptions(), array(
            'list_handlers' => $this->getListHandlers() ?: array('abstract_list', 'abstract_list_editable', 'abstract_list_creatable', 'abstract_list_deletable','admin_school_index'),
        ));
    }

    public function getSchoolAddressAction()
    {
        $collection = Seven::getCollection('iwe_school/school')
                        ->filter('longitude',array('null' => 'null'));

        foreach($collection as $school)
        {
            if( $school->getAddress() )
            {
                $address = $school->getAddress();
                $this->_saveSchoolCoords($address,$school);

            } else {
                $address = $school->getCity();
                $this->_saveSchoolCoords($address,$school);
            }
        }
    }

    protected function _getCoorsByYandex($address)
    {
        $address = urlencode($address);
        $url = "http://geocode-maps.yandex.ru/1.x/?geocode=".$address;
        $content = file_get_contents($url);
        preg_match('/<pos>(.*?)<\/pos>/',$content, $point);
        if(!isset($point[0]))
            return ;
        $coordinates = explode(" ", $point[0]);
        $longitude = (float) str_replace('<pos>','',$coordinates[0]);
        $latitude = (float) str_replace('</pos>','',$coordinates[1]);
        return $this->_checkCoords($longitude,$latitude);
    }

    protected function _getCoordsByGoogle($address)
    {
        $address = urlencode($address);
        $url = "http://maps.google.com/maps/api/geocode/json?address=".$address."&sensor=false";
        $content = file_get_contents($url);
        if(!$content)
            return null;
        $data = json_decode($content,true);
        if( isset($data['status']) && $data['status'] == 'OK' && isset($data['results']))
        {

            if( isset($data['results'][0]) )
            {
                $longitude = (float) $data['results'][0]['geometry']['location']['lng'];
                $latitude = (float) $data['results'][0]['geometry']['location']['lat'];
                return $this->_checkCoords($longitude,$latitude);
            }
        }
        return null;
    }

    protected function _checkCoords($longitude,$latitude)
    {
        if($longitude && $latitude)
        {
            if( ($longitude <= 39 && $longitude >= 22) && ($latitude <= 52 && $latitude >= 44 ) )
                return new Seven_Object(array('longitude' => $longitude,'latitude' => $latitude));
        }
        return null;
    }

    protected function _saveSchoolCoords($address,$school)
    {
        $coords = $this->_getCoorsByYandex($address);
        if(!$coords)
        {
            $coords = $this->_getCoordsByGoogle($address);
            if(!$coords)
                return;
        }
        $school->setLongitude($coords->getLongitude())
            ->setLatitude($coords->getLatitude())
            ->save();
    }
}