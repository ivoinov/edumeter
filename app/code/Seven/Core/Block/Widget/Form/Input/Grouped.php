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

class Core_Block_Widget_Form_Input_Grouped extends Core_Block_Widget_Form_Input_Abstract {

	public function __construct($data = array()) {
		if(!isset($data['template']))
			$data['template'] = 'widgets/form/input/grouped.phtml';
		parent::__construct($data);
	}

	public function prepare() {
		foreach($this->getInputs() as $input) {
			$input->prepare();
		}
		return parent::prepare();
	}

	protected $_inputs = array();

	public function getInputs() {
		if(!$this->_inputs) {
			foreach($this->getInputModel()->getInputs() as $index => $input) {
				$this->_inputs[$index] = Seven::getBlock($input->getBlockAlias(), $input->getData());
				$this->_inputs[$index]->setInputModel($input);
			}
		}
		return $this->_inputs;
	}
	
	public function getInput($name) {
		$inputs = $this->getInputs();
		if(empty($inputs[$name]))
			return null;
		return $inputs[$name];
	}
	
}