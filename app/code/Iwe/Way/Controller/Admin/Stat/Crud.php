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
        $years = array(2008, 2009, 2010, 2011, 2012);
        $schoolCollection = Seven::getCollection('iwe_school/entity');
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
}