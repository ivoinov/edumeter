<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ilvoi
 * Date: 7/30/13
 * Time: 9:59 AM
 * To change this template use File | Settings | File Templates.
 */
require "../app/seven.php";

calculateFromStatAction();
function calculateFromStatAction()
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
        $ratesArray = _proceesRateArray($ratesArray);
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

function _proceesRateArray($rateArray)
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