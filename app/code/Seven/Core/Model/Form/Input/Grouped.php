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

class Core_Model_Form_Input_Grouped extends Core_Model_Form_Input_Abstract {

	protected $_inputs = array();
	
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->addValidator('items', array($this, '_isItemsValid'));
	}

	public function getInputs() {
		if(empty($this->_inputs)) {
			foreach($this->getFields() as $id => $data) {
				if(!isset($data['type']))
					$data['type'] = 'text';
				$this->_inputs[$id] = Seven::getModel('core/form_input_' . $data['type'], $data)
					->setName($this->getName() . "[" . $id . "]")
					->setHtmlName($this->getName() . "[" . $id . "]")
					->setForm($this->getForm());
			}
		}
		return $this->_inputs;
	}

	public function setValue($value) {
		foreach($this->getInputs() as $name => $input) {
			if(isset($value[$name]))
			$input->setValue($value[$name]);
		}
		return parent::setValue($value);
	}

	public function getValue() {
		$value = array();
		foreach($this->getInputs() as $name => $input) {
			$value[$name] = $input->getValue();
		}
		return $value;
	}

	public function load() {
		foreach($this->getInputs() as $name => $input) {
			$input->load();
		}
		return $this;
	}

	public function _isItemsValid() {
		$valid = true;
		foreach($this->getInputs() as $name => $input) {
			$valid = $input->setRequired($this->getRequired())->isValid() && $valid;
		}
		return $valid;
	}

    public function save(){
    	$values = array();
    	foreach($this->getInputs() as $index => $input) {
    		$input->save();
    		$values[$index] = $input->getValue();
    	}
    	$this->setValue($values);
    	return parent::save(); 
    }
}