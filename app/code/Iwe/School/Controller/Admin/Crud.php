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
        $file = BP . DS . 'convert' . DS . 'convert_2012MDMAT.txt';
        $handle = fopen($file,'r+');
        while(!feof($handle))
        {
            $line = fgetcsv($handle,1024,';');
            $district = Seven::getModel('iwe_district/entity')->load(substr($line[0],0,8),'additional_id');
            $region = Seven::getModel('iwe_region/entity')->load(substr($line[1],0,2),'additional_id');
            if(isset($line[13]))
                $passedNumber = (int)$line[13];
            if(!$district->isLoaded() || !$region->isLoaded())
            {
                var_dump($line);
                echo "<hr/>";
                continue;
            }
            $stat = Seven::getModel('iwe_ratings/entity')
                            ->setYear(2012)
                            ->setSchoolName($line[2])
                            ->setSchoolDistrict($district->getName())
                            ->setSchoolRegion($region->getName())
                            ->setSubject('Українська мова')
                            ->setWay('Точні науки')
                            ->setPassedNumber($passedNumber)
                            ->setInterval1((int) round($line[3]/ 100 * $passedNumber ,2) )
                            ->setInterval2((int) round($line[4]/ 100 * $passedNumber ,2))
                            ->setInterval3((int) round($line[5]/ 100 * $passedNumber ,2))
                            ->setInterval4((int) round($line[6]/ 100 * $passedNumber ,2))
                            ->setInterval5((int) round($line[7]/ 100 * $passedNumber ,2))
                            ->setInterval6((int) round($line[8]/ 100 * $passedNumber ,2))
                            ->setInterval7((int) round($line[9]/ 100 * $passedNumber ,2))
                            ->setInterval8((int) round($line[10]/ 100 * $passedNumber,2))
                            ->setInterval9( (int)(round( $line[11]/ 100 * $passedNumber ,2) + round( $line[12]/ 100 * $passedNumber ,2)) )
                            ->save();
        }

    }
    public function getSchoolAddressAction()
    {
        $collection = Seven::getCollection('iwe_school/entity')
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

    public  function assignSchoolAction()
    {
        $schoolCollection = Seven::getCollection('iwe_school/entity');
        foreach($schoolCollection as $id => $school)
        {
            if($id == 0 || $id == 32 || $id == 33)
                continue;
            $schoolNumber = array();
            preg_match('/№[0-9]*/',$school->getName(),$schoolNumber);
            if( isset($schoolNumber[0]) )
                $statEntityCollection = Seven::getCollection('iwe_ratings/entity')
                                     ->filter('school_name',array('like' => '%'.$schoolNumber[0]))
                                     ->filter('school_district',array('like' => '%' . $school->getCity() . '%' ))
                                     ->filter('school_region',array('like' => '%'.$school->getCity() . '%' ))
                                     ->filter('year',2012);
            if(!count($statEntityCollection))
                $statEntityCollection = Seven::getCollection('iwe_ratings/entity')
                    ->filter('school_name',array('like' => '%'.$schoolNumber[0].'%'))
                    ->filter('school_district',array('like' => '%' . $school->getCity() . '%' ))
                    ->filter('school_region',array('like' => '%'.$school->getCity() . '%' ))
                    ->filter('year',2009);
            foreach( $statEntityCollection as $rate)
            {
                $subjectModel = Seven::getModel('iwe_subject/entity');

                $schoolStatRate = 0;
                $rateArray = array(
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6,
                    '7' => 7,
                    '8' => 8,
                    '9' => 9);
                foreach($rateArray as $coef)
                {
                    $interval = (int) $rate->getData('interval'.$coef);
                    $schoolStatRate += $interval * (int)$coef;
                }
                $schoolRate = Seven::getModel('iwe_ratings/subject_rate');
                $subjectModel->load($rate->getSubject(),'name');
                $schoolRate->setSchoolId($school->getId())
                           ->setYear($rate->getYear())
                           ->setSubject($subjectModel->getId())
                           ->setRate(round($schoolStatRate / $rate->getPassedNumber(),4))
                           ->setPassedNumber($rate->getPassedNumber())
                           ->save();
            }
        }
    }

    public function calculateRateAction()
    {
        $schoolCollection = Seven::getCollection('iwe_school/school')->getOwn();
        foreach($schoolCollection as $school)
        {
            $years = array(2008,2009,2010,2011);
            foreach($years as $year)
            {
                $statCollection = Seven::getCollection('iwe_ratings/rating')
                                ->filter('school_id',$school->getId())
                                ->filter('year',$year);

                $subjects = array();
                $rateYear = 0;
                if(count($statCollection)) {
                    foreach($statCollection as $stat)
                    {
                        if(isset($subjects[$stat->getSubjectId()]))
                            continue;
                        $rateYear += ($stat->getRate() / $stat->getCount() );
                        $subjects[$stat->getSubjectId()] = $stat->getSubjectId();
                    }

                $rate = Seven::getModel('iwe_school/rate')
                    ->setSchoolId($school->getId())
                    ->setYear($year)
                    ->setRate($rateYear / count($subjects))
                    ->save();
                }
            }
        }
    }
}