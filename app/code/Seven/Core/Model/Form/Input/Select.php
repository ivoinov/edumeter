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

class Core_Model_Form_Input_Select extends Core_Model_Form_Input_Abstract{
    
    public function __construct($data = array()) {
        parent::__construct($data);
    	$this->addValidator('exists', array($this,'_isExists'));
    }
    
    public function getOptions() {
		$options = parent::getOptions();
		if(!is_array($options)) {
	            $method = "getOptionsArray";
	            if(strpos($options, "::") !== false)
	                list($options, $method) = explode("::", $options);
		    $options = call_user_func(array(Seven::getModel($options), $method), $this);
		    parent::setOptions($options);
		}
		return $options;
    }
    
    public function _isExists(){
        $options = $this->getOptions();
        if($this->getOptional() && empty($options))
        	return true;
        if(!array_key_exists($this->getValue(), $options)) {
            $this->setErrorMessage(__("Value is not exists"));
            $this->setErrorCode(4);
            return false;
        }
        return true;
    }
}
