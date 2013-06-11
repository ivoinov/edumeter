<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ilya Voinov
 * User email: ilya.voinov@yahoo.com
 * Date: 6/11/13  
 */
class Iwe_Voice_Block_Voice extends Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('user/voice.phtml');
    }

    public function prepare()
    {
        $this->getLayout()->getBlock('head')->addJs('js/user/voice.js');
        parent::prepare();
    }
}