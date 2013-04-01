<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   Seven
 * @package    Core
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Core_Block_Message extends Core_Block_Template{
       
    public function __construct($data = array()) {
        parent::__construct($data);
        if(empty($data['template']))
            $data['template'] = 'page/message.phtml';       
    }

    public function getMessages($type) {
        $messages = Seven::getSingleton("core/session")->getMessages($type);
        Seven::getSingleton("core/session")->flushMessages($type);
        return $messages ? (array) $messages : array();
    }
    
    protected function _toAjax() {
    	$messages = array();
    	foreach(array('error', 'success', 'info', 'notice') as $type)
    		$messages[$type] = Seven::getSingleton("core/session")->getMessages($type);
        Seven::getSingleton("core/session")->flushMessages($type);
    	return $messages;
    }
    
}
