<?php

class Iwe_School_Resource_School_Collection extends Core_Resource_Entity_Collection {

    public function getOwn()
    {
        return $this->filter('city',array('like' => '%Київ%'))->orFilter('city',array('like' => '%Харків%'));
    }
}
