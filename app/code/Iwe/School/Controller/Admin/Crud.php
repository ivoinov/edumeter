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

    public function getSchoolAddressAction()
    {
        $collection = Seven::getCollection('iwe_school/school');
        foreach($collection as $school)
        {
            if( $school->getAddress() )
            {
                $address = $school->getCity() . ', ' . $school->getAddress();
                $coords = $this->_getCoorsByAddress($address);
            } else {

            }
        }
    }

    protected function _getCoorsByAddress($address)
    {
        $address = urlencode($address);
        $url = "http://geocode-maps.yandex.ru/1.x/?geocode=".$address;
        $content = file_get_contents($url);
        preg_match('/<pos>(.*?)<\/pos>/',$content, $point);
        $coordinates = explode(" ", $point[0]);
        $coordinates[0] = str_replace('<pos>','',$coordinates[0]);
        $coordinates[1] = str_replace('</pos>','',$coordinates[1]);
        return array($coordinates[0],$coordinates[1]);
    }

}