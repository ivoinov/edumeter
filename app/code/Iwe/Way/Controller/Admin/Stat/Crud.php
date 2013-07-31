<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 5/6/13
 * Time: 12:15 PM
 */
class Iwe_Way_Controller_Admin_Stat_Crud extends Core_Controller_Crud_Crud
{

    public function indexAction()
    {
        $this->getLayout()->addTag("admin_school_way_stat_index");
        parent::indexAction();
    }

    public function updateStatAction()
    {
        set_time_limit(0);
        $years = array(2011);
        $schoolCollection = Seven::getCollection('iwe_school/school');
        foreach ($schoolCollection as $school) {
            foreach ($years as $year) {
                foreach (Seven::getCollection('iwe_way/entity') as $way) {
                    $wayRate = 0;
                    $count = 0;
                    $waySubjects = $way->getSubjects();
                    foreach ($waySubjects[''] as  $subject) {
                        $ratingsCollection = Seven::getCollection('iwe_ratings/subject_rate')
                            ->filter('school_id', $school->getId())
                            ->filter('year', $year)
                            ->filter('subject', $subject);
                        if(count($ratingsCollection)) {
                            $wayRate += (float)$ratingsCollection->first()->getRate();
                            $count++;
                        }
                    }
                    if($wayRate) {
                        $wayRate = $wayRate / (int)$count;
                        $collection = Seven::getCollection('iwe_way/stat')
                                        ->filter('school',$school->getId())
                                        ->filter('way',$way->getId())
                                        ->filter('year',$year);
                        if(count($collection))
                            $collection->first()
                                        ->setRate(round($wayRate,4))
                                        ->save();
                        else
                            Seven::getModel('iwe_way/stat')
                            ->setWay($way->getId())
                            ->setSchool($school->getId())
                            ->setYear($year)
                            ->setRate(round($wayRate,4))
                            ->save();
                    }
                }
            }
        }
        $this->redirect(seven_url('*/*/'));
    }

    public function calculateFromStatAction()
    {
        set_time_limit(0);
	ini_set('memory_limit','1024M');
        $schoolCollection = Seven::getCollection('iwe_school/school');
        foreach($schoolCollection as $schoolModel) {
            $schoolWayStatCollection = Seven::getCollection('iwe_way/stat')
                ->filter('school', $schoolModel->getId());
            $ratesArray = array();
            foreach($schoolWayStatCollection as $wayModel) {
                if(!isset($ratesArray[$wayModel->getWay()])) {
                    $ratesArray[$wayModel->getWay()] = array();
                    $ratesArray[$wayModel->getWay()][] = $wayModel->getRate();
                } else {
                    $ratesArray[$wayModel->getWay()][] = $wayModel->getRate();
                }
            }
            $ratesArray = $this->_proceesRateArray($ratesArray);
            foreach($ratesArray as $wayId => $rateValue) {
                $wayRateModel = Seven::getModel('iwe_way/stat')
                            ->setWay($wayId)
                            ->setSchool($schoolModel->getId())
                            ->setYear(2010)
                            ->setRate($rateValue)
                            ->setFrom(1)
                            ->save();
                unset($wayRateModel);
            }
        }
    }

    protected function _proceesRateArray($rateArray)
    {
        $result = array();
        foreach($rateArray as $wayId => $rateData) {
            $rateValue = 0;
             foreach ($rateData as $rate) {
                 $rateValue += $rate;
             }
            $result[$wayId] = round($rateValue / count($rateData), 2);
        }
        return $result;
    }
}
