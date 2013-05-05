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

class Core_Block_Widget_Form_Input_Select extends Core_Block_Widget_Form_Input_Abstract {
	
	public function getHtmlAttributes() {
		$attributes = parent::getHtmlAttributes();
		if($this->getMultiple())
			$attributes['multiple'] = "";
		if($this->getSize())
			$attributes['size'] = $this->getSize();
		return $attributes;
	}
	
	public function getOptions() {
		$options = parent::getOptions();
		if(! is_array($options)) {
			if($this->getInputModel()) {
				$options = $this->getInputModel()->getOptions();
			} else {
				$method = "getOptionsArray";
				if(strpos($options, "::") !== false)
					list($options, $method) = explode("::", $options);
				$options = call_user_func(array(Seven::getModel($options), $method), $this);
			}
			parent::setOptions($options);
		}
		return $options;
	}
	
	protected function _getSelectOptions() {
		$options = $this->getOptions();
		$options_html = array();
		if($this->getPlaceholder())
				$options_html[] = "<option value='" . $this->getPlaceholderValue() . "'>" . $this->getPlaceholder() . "</option>";
            foreach($options as $key => $value)
                if(!is_array($this->getValue()))
                    $options_html[] = "<option value='" . $key . "'" . ((string)$this->getValue() == (string)$key ? " selected" : "") . ">" . $value . "</option>";
                else {
                    $options_html[] = "<option value='" . $key . "'" . (in_array($key,$this->getValue()) ? " selected" : "") . ">" . $value . "</option>";
                }
		return implode('', $options_html);
	}
	
	protected function _toHtml() {
		return "<select " . $this->getAttributeString() . ">" . $this->_getSelectOptions() . "</select>";
	}
}