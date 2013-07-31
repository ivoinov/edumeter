<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 4/21/13
 * Time: 7:57 PM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Block_List extends Core_Block_Widget_Grid_Xml
{
    const CACHE_CONFIG_KEY_WAY_RATE = 'WAY_RATE';
    const CACHE_CONFIG_KEY_SUBJECT_RATE = 'SUBJECT_RATE';

    public function __construct($data = array())
    {
        if(!isset($data['use_ajax']))
            $data['use_ajax'] = 1;
        parent::__construct($data);
        $this->removeFilter('pager');
        $this->addFilter("radius", array('type' => 'iwe_school/radius', 'filter_priority' => 0));
        $this->addFilter("way", array('type' => 'iwe_school/way', 'filter_priority' => 0));
        $this->addFilter("year", array('type' => 'iwe_school/year', 'filter_priority' => 0));
        $this->addFilter("rate",array('type' => 'iwe_school/rate', 'filter_priority' => 999));
        if($radius = $this->getFilter("radius")) {
            $radius->setTemplate("school/filters/radius.phtml");
        }
        if($way = $this->getFilter("way")) {
            $way->setTemplate("school/filters/way.phtml");
        }
        if($year = $this->getFilter("year")) {
            $year->setTemplate("school/filters/year.phtml");
        }
    }

    public function getContentJson()
    {
        return json_encode($this->getContentArray());
    }

    protected function _toAjax()
    {
        return $this->getContentArray();
    }

    public function getContentArray()
    {
        $content = array('items' => array());
        $content_row = array();
        foreach($this->getCollection() as $row) {
            if(!$row->_getId())
                continue;
            $content_row['id'] = $row->_getId();
            $content_row['hash'] = md5($row->getName());
            foreach($this->getColumns() as $id => $column) {
                $content_row[$id] = $column->getCellValue($row);
            }
            $content_row['rate'] = round(($row->getData('rate')) ? $row->getData('rate') : $row->getRate()) ;
            $content_row['description'] = $row->getDescription();
            $content_row['waysChart'] = $this->_getWaysRate($row->getSchool(), $row->getYear());
            $content_row['subjectCharts'] = $this->_getSubjectRate($row->getSchool());
            $content['items'][] = $content_row;

        }
        return $content;
    }

    protected function _getWaysRate($schoolId, $year)
    {
        $cacheId = $schoolId . '_'.self::CACHE_CONFIG_KEY_WAY_RATE;
        $result = Seven::cache()->load($cacheId);
        if(!empty($result)) {
            return $result;
        }
        else {
            $result = array();
        }
        $wayStatCollection = Seven::getCollection('iwe_way/stat')
                ->filter('way',array('neq' => 2))
                ->filter('school',$schoolId)
                ->filter('year', $year);
        foreach($wayStatCollection as $wayStatObject) {
            $rate = round($wayStatObject->getRate());
            $result[] = array(
                'rate' => $rate,
                'color' => $this->_getLineColor($rate),
                'width' => $this->_getLineWidth($rate),
                'name'  => Seven::getModel('iwe_way/entity')->load($wayStatObject->getWay())->getName()
            );
        }
        Seven::cache()->save($result, $cacheId, array(md5('school_way_rate')));
        return $result;
    }

    protected function _getLineColor($rate)
    {
        $color = 'green';
        $rate = round($rate);
        if($rate < 150)
            $color = 'red';
        if($rate >= 150 && $rate < 170)
            $color = 'yellow';
        if($rate >= 170)
            $color = 'green';
        return $color;
    }

    protected function _getLineWidth($rate)
    {
        return round(($rate - 100) * 200 / 100);
    }

    protected function _getSubjectRate($schoolId)
    {
        $cacheId = $schoolId . '_'.self::CACHE_CONFIG_KEY_SUBJECT_RATE;
        $result = Seven::cache()->load($cacheId);
        if(!empty($result))
            return $result;
        $years = array(2010,2011,2012);
        $row = array();
        foreach($years as $year) {
            $subjectRateCollection = Seven::getCollection('iwe_ratings/subject_rate')
                ->filter('school_id', $schoolId)
                ->filter('year', $year);
            foreach($subjectRateCollection as $subjectRateObject) {
                $rate = round($subjectRateObject->getRate());
                $subject = Seven::getModel('iwe_subject/entity')->load($subjectRateObject->getSubject());
                if(!isset($row[$subject->getId()])) {
                    $row[$subject->getId()]['subjectName'] = $subject->getName();
                }
                $row[$subject->getId()]['years'][$subjectRateObject->getYear()]['rate'] = $rate;
                $row[$subject->getId()]['years'][$subjectRateObject->getYear()]['color'] = $this->_getLineColor($rate);
                $row[$subject->getId()]['years'][$subjectRateObject->getYear()]['width'] = $this->_getLineWidth($rate);
            }
        }
        foreach($row as $subjectId => $subjectRow) {
            foreach($years as $year) {
                if(!isset($subjectRow['years'][$year])) {
                    $subjectRow['years'][$year]['rate'] = 0;
                    $subjectRow['years'][$year]['color'] = 'red';
                    $subjectRow['years'][$year]['width'] = 0;
                }
            }
            ksort($subjectRow['years']);
            $row[$subjectId] = $subjectRow;
        }
        Seven::cache()->save($row, $cacheId, array(md5('school_subject_rate')));
        return $row;
    }
}