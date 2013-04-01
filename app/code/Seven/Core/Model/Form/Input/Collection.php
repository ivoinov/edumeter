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

class Core_Model_Form_Input_Collection extends Core_Model_Form_Input_Abstract {
	
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->addValidator('items', array($this, '_isItemsValid'));
	}
	
	public function _isItemsValid($value, $input) {
		$valid = true;
		foreach($input->getInputs(false) as $index => $input) {
			$valid = $input->isValid() && $valid;
		}
		return $valid;
	}
	
	public function getIndexes() {
		return $this->isSubmit() ? array_keys((array)$this->_getRequestValue()) : array_keys((array)$this->getValue());
	}
	
	protected $_inputs = array();
	
	protected function _getSource() {
		return $this->getItemInputInstance()->getSource();
	}
	
	public function getItemInputInstance() {
		$item_data = $this->getItem();
		if(empty($item_data['type']))
			$item_data['type'] = 'text';
		$item_data['form'] = $this->getForm();
		return Seven::getModel('core/form_input_' . $item_data['type'], $item_data);
	}
	
	public function getInputs($with_template = true) {
		if(!$this->_inputs) {
			foreach($this->getIndexes() as $index) {
				$this->_inputs[$index] = $this->getItemInputInstance()
					->setName($this->getName() . "[$index]")
					->setHtmlName($this->getName() . "[$index]")
					->setIndex($index);
			}
			$this->_inputs['__template__'] = $this->getItemInputInstance()
				->setName($this->getName() . "[__template__]")
				->setHtmlName($this->getName() . "[__template__]");
		}
		$inputs = $this->_inputs;
		if(!$with_template)
			unset($inputs['__template__']);
		return $inputs;
	}
	
	public function setValue($value) {
		parent::setValue($value);
		foreach($this->getInputs(false) as $index => $input) {
			if(isset($value[$index]))
				$input->setValue($value[$index]);
		}
		return $this;
	}
	
	protected function _getRequestValue() {
      	$value = parent::_getRequestValue();
      	unset($value['__template__']);
      	return $value;
    }

    public function save(){
    	$values = array();
    	foreach($this->getInputs(false) as $index => $input) {
    		$input->save();
    		$values[$index] = $input->getValue();
    	}
    	$this->setValue($values);
    	return parent::save(); 
    }
    
    public function load(){
    	foreach($this->getInputs(false) as $index => $input) {
    		$input->load();
    	}
    	return $this; 
    }
    
}