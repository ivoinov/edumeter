<?php

class Iwe_School_Block_Map extends  Core_Block_Template
{
    public function getSchoolsArray()
    {
        $result = array('items' => array());
        foreach (Seven::getCollection('iwe_school/school') as $row)
        {
            $content_row =  array(
                'id' => $row->getId(),
                'name' => $row->getName(),
                'region' => $row->getRegion(),
                'city' => $row->getCity(),
                'address' => $row->getAddress(),
                'phone' => $row->getPhone(),
                'description' => $row->getDescription()
            );
            $result['items'][] = $content_row;
            return json_encode($result);
        }
    }
}