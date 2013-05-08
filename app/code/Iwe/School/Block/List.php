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
            $content['items'][] = $content_row;
        }
        return $content;
    }
}