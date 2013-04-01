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

    class Core_Model_Form_Input_Password extends Core_Model_Form_Input_Text {
    	
    	public function __construct($data = array()) {
    		if(!isset($data['encrypt'])) 
    			$data['encrypt'] = 'md5';
    		parent::__construct($data);
    	}
    	
    	public function save(){
    		$this->setValue($this->_encryptValue($this->getValue()));
    		return parent::save();
    	}
    	
    	public function load(){
    		$this->setValue(''); // get value only from request, and skip value stored in source
			return parent::load();
    	}
    	
    	protected function _encryptValue($value) {
    		$method = $this->getEncrypt() ? $this->getEncrypt() : 'md5';
    		return Seven::getHelper('core/encrypt')->encrypt($value, $method); 
    	}
    	
    }