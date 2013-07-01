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
        $result = array();
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

    protected function _getLineHeight($rate)
    {
        $height = 0;
        return round(100 * ($rate - 100) / 100);
    }
    protected function _getSubjectRate($schoolId)
    {
        $result = array();
        $subjectRateCollection = Seven::getCollection('iwe_ratings/subject_rate')
            ->filter('school_id',$schoolId);
        foreach($subjectRateCollection as $subjectRateObject) {
            $row = array();
            $rate = round($subjectRateObject->getRate());
            $row['subjectName'] = Seven::getModel('iwe_subject/entity')->load($subjectRateObject->getSubject())->getName();
            $row['years'][$subjectRateObject->getYear()]['rate'] = $rate;
            $row['years'][$subjectRateObject->getYear()]['color'] = $this->_getLineColor($rate);
            $row['years'][$subjectRateObject->getYear()]['height'] = $this->_getLineHeight($rate);
            $row['years'][2010]['rate'] = rand(120,200);
            $row['years'][2010]['height'] = $this->_getLineHeight(rand(120,200));
            $row['years'][2010]['color'] = $this->_getLineColor(rand(120,200));
            $row['years'][2011]['rate'] = rand(120,200);
            $row['years'][2011]['height'] = $this->_getLineHeight(rand(120,200));
            $row['years'][2011]['color'] = $this->_getLineColor(rand(120,200));
            ksort($row['years']);
            $result[] = $row;
        }
        return $result;
    }
}