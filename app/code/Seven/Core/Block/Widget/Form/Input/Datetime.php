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

class Core_Block_Widget_Form_Input_Datetime extends Core_Block_Widget_Form_Input_Abstract {

	public function getHtmlAttributes() {
		
		$this->addHtmlClass('widget-input-datetime');
		$attributes = parent::getHtmlAttributes();
		$attributes['wrap'] = $this->getWrap();
		$attributes['cols'] = $this->getCols();
		$attributes['rows'] = $this->getRows();
		$attributes['type'] = "text";
		$attributes['value'] = $this->getValue();
		return $attributes;
	}
	
	protected function _toHtml() {
		return "<input " . $this->getAttributeString() . ">";
	}
	
	public function prepare() {
		if($head = $this->getLayout()->getBlock('head'))
			$head->addJs('jquery/jquery.js', 'lib')
				->addJs('jquery/jquery-ui.js', 'lib')
				->addJs('jquery/jquery.ui.datepicker.js', 'lib')
				->addJs('jquery/jquery.ui.timepicker.js', 'lib')
				->addJs('seven/widgets/inputs/datepicker.js', 'lib')
				->addCss('jquery-ui/jquery-ui.css', 'skin');
		
		return $this;
	}
}
