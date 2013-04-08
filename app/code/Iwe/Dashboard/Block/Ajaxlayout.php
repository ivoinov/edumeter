<?php
class Iwe_Dashboard_Block_Ajaxlayout extends Core_Block_Template {

    public function __construct($data = array()) {
        if(!isset($data['template']))
            $data['template'] = 'page/ajax_loading_layout.phtml';
        parent::__construct($data);
    }

    public function prepare() {
        if($head = $this->getLayout()->getBlock('head'))
            $head->addJs('iwe/ajaxlayout.js', 'lib');
        return parent::prepare();
    }
}
