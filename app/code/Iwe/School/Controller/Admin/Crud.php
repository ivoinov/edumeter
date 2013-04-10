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

    /*Import schools from file*/
    public function processSchoolAction()
    {
        $file = BP . DS . 'var' . DS . 'schools' . DS . 'DISTRICT.txt';
        $ids = array();
        if($handle = fopen($file,'r+'))
        {
            while(!feof($handle)) {
               $data = fgetcsv($handle,1024,';');
               if( isset($data[1]) && trim($data[1],"'") == 14)
               {
                   $ids[$data[0]] = $data[2];
               }

            }
        }
        fclose($handle);
        $file = BP . DS . 'var' . DS . 'schools' . DS . 'RSCHOOL.txt';
        if($handle = fopen($file,'r+'))
        {
            while(!feof($handle)) {
                $data = fgetcsv($handle,1024,';');
                $id = str_replace('"','',$data[0]);
                $schoolId = ((int)$id ) ?  : (int)substr($id,3);
                if( !isset($data[18]) )
                {
                    var_dump($schoolId);
                } elseif ( !isset($ids[$data[18]]) )
                {
                    var_dump($schoolId);
                }
                else
                {
                    $schoolModel = Seven::getModel('iwe_school/school')
                        ->setId($schoolId )
                        ->setName($data[3])
                        ->setRegionId(12)
                        ->setCity( $ids[$data[18]] )
                        ->setAddress($data[11])
                        ->setDescription("Дирекція:" . $data[12] . "\n Школа:" . $data[5])
                        ->save();
                }
            }
        }
        fclose($handle);
    }

    public function exportFromCsvAction()
    {
        $filePath = BP . DS . 'var' . DS . 'stat' . DS . 'result';
        foreach(glob($filePath . DS . '*') as $file)
        {
            if($handle = fopen($file,'r+'))
            {
                while(!feof($handle))
                {
                    $lineData = fgetcsv($handle,1024,',');
                    $entity = Seven::getModel('iwe_ratings/entity');
                    $entity->setYear($lineData[0])
                           ->setSchoolName($lineData[1])
                           ->setSchoolType($lineData[2])
                           ->setSchoolDistrict($lineData[3])
                           ->setSchoolRegion($lineData[4])
                           ->setSubject($lineData[5])
                           ->setWay($lineData[6])
                           ->setPassedNumber($lineData[7])
                           ->setInterval1($lineData[8])
                           ->setInterval2($lineData[9])
                           ->setInterval3($lineData[10])
                           ->setInterval4($lineData[11])
                           ->setInterval5($lineData[12])
                           ->setInterval6($lineData[13])
                           ->setInterval7($lineData[14])
                           ->setInterval8($lineData[15])
                           ->setInterval9($lineData[16])
                           ->setInterval10($lineData[17])
                           ->save();
                }
                fclose($handle);
            }
            die();
        }
    }


    public function getSchoolAddressAction()
    {
        $collection = Seven::getCollection('iwe_school/school');
        foreach($collection as $school)
        {
            if( $school->getAddress() )
            {
                $address = $school->getCity() . ', ' . $school->getAddress();
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
            if( ($longitude <= 41) && ($latitude <= 52) )
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