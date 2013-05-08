<?php

class Iwe_School_Resource_Entity_Collection extends Core_Resource_Entity_Collection {

    public function getSelect()
    {
        if($this->_select === NULL) {
            $this->_select = parent::getSelect()
                ->joinl(array('way_stat_table' => 'iwe_stat_way'),'way_stat_table.school = main.id');
        }
        return $this->_select;
    }
    public function withRate($way = 2, $year = 2012)
    {
        return $this
                ->filter('way',$way)
                ->filter('year',$year)
                ->order('rate DESC');
    }

    public function getInCurrentRadius($currentLongitude,$currentLatitude,$radius)

    {
        if(!$currentLatitude || !$currentLongitude)
           return;
        $dist = sprintf(
            "(%s*acos(cos(radians(%s))*cos(radians(`latitude`))*cos(radians(`longitude`)-radians(%s))+sin(radians(%s))*sin(radians(`latitude`))))",
            6372795,
            $currentLatitude,
            $currentLongitude,
            $currentLatitude
        );
        return $this->filter($dist,array('to' => $radius));
    }
}
