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

class Core_Model_Form_Entity extends Core_Model_Form_Xml {
	
	protected $_source = NULL;
	
	protected function _load($source = array()) {
		if($source instanceof Core_Model_Entity)
			$this->_source = $source;
		
		if($source instanceof Core_Model_Multiview_Entity && $source->_getView()) {
			foreach($source->_getMultiviewFields() as $key => $description) {
				if(($input = $this->getField($key)) && $key != $source->_getKey())
					$input->setUseDefault($source->_isDefault($key) ? true : false);
			}
		} 
	
	}
	
	public function getEntity() {
		return $this->_source;
	}
	
	protected function _save() {
		if($this->_source) {
			$this->_source->addData($this->getValues())->save();
		}
	}

}