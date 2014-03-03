<?php

/**
 * Created by PhpStorm.
 * User: b.soroka
 * Date: 2/24/14
 * Time: 2:03 PM
 */
class Iwe_School_Block_Table extends Iwe_School_Block_List
{
    public function getWayCollection()
    {
        return Seven::getCollection('iwe_way/entity');
    }

    public function getRegions()
    {
        return Seven::getCollection('iwe_region/entity');
    }

    public function getDistricts()
    {
        return Seven::getCollection('iwe_district/entity');
    }
}