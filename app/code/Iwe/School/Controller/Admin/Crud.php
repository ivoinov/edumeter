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
    public function scriptAction()
    {
        set_time_limit(0);
        $file = BP . DS . 'var' . DS . 'stat' . DS . '2010' . DS . '2010 BIO matching edumeter.txt';
        $handle = fopen($file, 'r');
        $failCount = 0;
        $count = 0;
        while(!feof($handle)) {
            $statEntity = Seven::getModel('iwe_ratings/entity');
            $line = fgetcsv($handle,2048,';');

            if($count == 0)
                $school = Seven::getModel('iwe_school/school')->load(substr($line[0],3,strlen($line[0])));
            else
                $school = Seven::getModel('iwe_school/school')->load($line[0]);
            if(!$school->isLoaded()) {
                var_dump($line);
                echo "<hr/>";
                $failCount++;
                continue;
            }
            $statEntity
                ->setYear(2010)
                ->setSchoolName($school->getName())
                ->setDistrictId($school->getDistrictId())
                ->setRegionId($school->getRegionId())
                ->setSubjectId(2)
                ->setWayId(5)
                ->setPassedNumber($line[14])
                ->setInterval2(round($line[4] * (int)$line[14] / 100 , 2))
                ->setInterval3(round($line[5] * (int)$line[14] / 100 , 2))
                ->setInterval4(round($line[6] * (int)$line[14] / 100 , 2))
                ->setInterval5(round($line[7] * (int)$line[14] / 100 , 2))
                ->setInterval6(round($line[8] * (int)$line[14] / 100 , 2))
                ->setInterval7(round($line[9] * (int)$line[14] / 100 , 2))
                ->setInterval8(round($line[10] * (int)$line[14] / 100 , 2))
                ->setInterval9(round($line[11] * (int)$line[14] / 100 , 2))
                ->setInterval10(round($line[12] * (int)$line[14] / 100 , 2))
                ->setInterval10(round($line[13] * (int)$line[14] / 100 , 2))
                ->save();
            $count++;
        }
        fclose($handle);
        echo "<h1><strong>".$failCount."</strong></h1>";
        echo "<br/><h1><strong>".$count."</strong></h1>";
    }

    public function getSchoolAddressAction()
    {
        $collection = Seven::getCollection('iwe_school/school')
            ->filter('longitude', array('null' => 'null'))
            ->filter('latitude', array('null' => 'null'));

        foreach ($collection as $school) {
            if ($school->getAddress()) {
                $district = Seven::getModel('iwe_district/entity')->load($school->getDistrictId());
                $region = Seven::getModel('iwe_region/entity')->load($district->getRegionId());
                $address = $region->getName().', '.$district->getName(). ', '.$school->getAddress();
                $this->_saveSchoolCoords($address, $school);

            } else {
                $address = $school->getCity();
                $this->_saveSchoolCoords($address, $school);
            }
        }
    }

    protected function _getCoorsByYandex($address)
    {
        $address = urlencode($address);
        $url = "http://geocode-maps.yandex.ru/1.x/?geocode=" . $address;
        $content = file_get_contents($url);
        preg_match('/<pos>(.*?)<\/pos>/', $content, $point);
        if (!isset($point[0]))
            return;
        $coordinates = explode(" ", $point[0]);
        $longitude = (float)str_replace('<pos>', '', $coordinates[0]);
        $latitude = (float)str_replace('</pos>', '', $coordinates[1]);
        return $this->_checkCoords($longitude, $latitude);
    }

    protected function _getCoordsByGoogle($address)
    {
        $address = urlencode($address);
        $url = "http://maps.google.com/maps/api/geocode/json?address=" . $address . "&sensor=false";
        $content = file_get_contents($url);
        if (!$content)
            return null;
        $data = json_decode($content, true);
        if (isset($data['status']) && $data['status'] == 'OK' && isset($data['results'])) {

            if (isset($data['results'][0])) {
                $longitude = (float)$data['results'][0]['geometry']['location']['lng'];
                $latitude = (float)$data['results'][0]['geometry']['location']['lat'];
                return $this->_checkCoords($longitude, $latitude);
            }
        }
        return null;
    }

    protected function _checkCoords($longitude, $latitude)
    {
        if ($longitude && $latitude) {
            if (($longitude <= 39 && $longitude >= 22) && ($latitude <= 52 && $latitude >= 44))
                return new Seven_Object(array('longitude' => $longitude, 'latitude' => $latitude));
        }
        return null;
    }

    protected function _saveSchoolCoords($address, $school)
    {
        $coords = $this->_getCoorsByYandex($address);
        if (!$coords) {
            $coords = $this->_getCoordsByGoogle($address);
            if (!$coords)
                return;
        }
        $school->setLongitude($coords->getLongitude())
            ->setLatitude($coords->getLatitude())
            ->save();
    }

    public function assignSchoolAction()
    {
        $schoolCollection = Seven::getCollection('iwe_school/school');
        foreach ($schoolCollection as $id => $school) {
            $statEntityCollection = Seven::getCollection('iwe_ratings/entity')
                ->filter('school_name', array('like' => '%' . $school->getName() . '%'))
                ->filter('year', 2012);
            if (!count($statEntityCollection)) {
                continue;
            }

            foreach ($statEntityCollection as $rate) {
                $subjectModel = Seven::getModel('iwe_subject/entity');

                $schoolStatRate = 0;
                $rateArray = array(
                    '1' => 111.75,
                    '2' => 129.75,
                    '3' => 143,
                    '4' => 156,
                    '5' => 167.25,
                    '6' => 178,
                    '7' => 186.75,
                    '8' => 192.75,
                    '9' => 197.25,
                    '10'=> 200
                );
                foreach ($rateArray as $id => $coef) {
                    $interval = (int)$rate->getData('interval' . $id);
                    $schoolStatRate += $interval * (float)$coef;
                }
                $schoolRate = Seven::getModel('iwe_ratings/subject_rate');
                $schoolRate->setSchoolId($school->getId())
                    ->setYear($rate->getYear())
                    ->setSubject($rate->getSubjectId())
                    ->setRate(round($schoolStatRate / $rate->getPassedNumber(), 4))
                    ->setPassedNumber($rate->getPassedNumber())
                    ->save();
            }
        }
    }

    public function updateDistrictAction()
    {
        set_time_limit(0);
        $filename = BP . DS . 'retions_2012_4.txt';
        if($handle = fopen($filename,'r')) {
            while(!feof($handle)) {
                $line = fgetcsv($handle,2048,';');
                $districtModel = Seven::getModel('iwe_district/entity');
                $region = Seven::getModel('iwe_region/entity')->load($line[1],'additional_id');
                $districtModel
                    ->setName(trim($line[2]))
                    ->setAdditionalId($line[0])
                    ->setRegionId($region->getId())
                    ->save();
            }
        }
        fclose($handle);
    }

    public function addSchoolAction()
    {
        $filepath = BP . DS . 'var' . DS . 'school' . DS . 'Zhitomir.txt';
        if($handle = fopen($filepath,'r')) {
            $count = 0;
            while(!feof($handle)) {
                $line = fgetcsv($handle,2048,';');

                if($count == 0)
                    $district = Seven::getModel('iwe_district/entity')->load(substr($line[0],3,strlen($line[0])),'additional_id');
                else
                    $district = Seven::getModel('iwe_district/entity')->load($line[0],'additional_id');
                $description = isset($line[6]) ? 'Телефон: ' . $line[6] :'';
                $name = explode('№',$line[1]);
                $schoolName = isset($name[1]) ? $name[0] . '№' . ltrim($name[1]) : $name[0];
                $school = Seven::getModel('iwe_school/entity')
                            ->setName($schoolName)
                            ->setRegionId($district->getRegionId())
                            ->setDistrictId($district->getId())
                            ->setCity($line[3])
                            ->setAddress($line[5])
                            ->setDescription( $description)
                            ->setSearchStr($line[2])
                            ->save();
                $count ++;
            }
        }
        fclose($handle);
    }
}