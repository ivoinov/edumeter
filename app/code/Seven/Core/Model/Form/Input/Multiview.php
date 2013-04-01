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

class Core_Model_Form_Input_Multiview extends Core_Model_Form_Input_Abstract {

	protected $_inputs = array();
	
	public function getRealInputs() {
		if(!$this->_inputs) {
			foreach(array(0, 1, 2, 3) as $code) {
				$type = $this->getViewType() ? $this->getType() : 'text';
				$this->_inputs[$code] = Seven::getModel('core/form_input_' . $type, $this->getData())
					->setHtmlName($this->getName() . '[' . $code . ']')
					->setType($type);
			}
		}
		return $this->_inputs;
	}
	
	public function isValid() {
		$valid = true;
		foreach($this->getRealInputs() as $input)
			$valid = $this->isValid() && $valid;
		return $valid;	
	}
	
	public function addValidator($id, $callback){
		foreach($this->getRealInputs() as $input)
			$input->addValidator($id, $callback);
		return $this;
	}
	
	public function removeValidator($id){
		foreach($this->getRealInputs() as $input)
			$input->removeValidator($id);
	}
	
	public function getValidators(){
		foreach($this->getRealInputs() as $input)
			return $input->getValidators();
	}
	
	public function load(){
		foreach($this->getRealInputs() as $input)
			$input->load();
	}
	
	public function isSubmit() {
		foreach($this->getRealInputs() as $input)
			return $input->isSubmit();
	}
	
}
