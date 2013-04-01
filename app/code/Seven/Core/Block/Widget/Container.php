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

class Core_Block_Widget_Container extends Core_Block_Template {
	
	protected $_buttons = array();
	protected $_sorted_buttons = false;
	
	public function __construct($data = array()) {
		if(! isset($data['template']))
			$data['template'] = 'widgets/container.phtml';
		parent::__construct($data);
	}
	
	public function getButtons() {
		if(! $this->_sorted_buttons) {
			usort($this->_buttons, function ($a, $b) {
				return $a->getOrder() - $b->getOrder();
			});
			$this->_sorted_buttons = true;
		}
		return $this->_buttons;
	}
	
	public function getButton($id) {
		if(isset($this->_buttons[$id]))
			return $this->_buttons[$id];
		return null;
	}
	
	public function addButton($id, $data) {
		$this->_sorted_buttons = false;
		$this->_buttons[$id] = Seven::getBlock(empty($data['class']) ? 'core/widget_button' : $data['class'], $data);
		return $this;
	}
	
	public function removeButton($id) {
		unset($this->_buttons[$id]);
		return $this;
	}

}