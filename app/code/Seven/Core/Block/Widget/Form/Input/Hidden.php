<?php

class Core_Block_Widget_Form_Input_Hidden extends Core_Block_Widget_Form_Input_Abstract {
    	
        public function getHtmlAttributes() {
		$attributes = parent::getHtmlAttributes();
		$attributes['value'] = $this->getValue();
		$attributes['type'] = "hidden";
		return $attributes;
	}
	
	protected function _toHtml() {
		return "<input " . $this->getAttributeString() . ">\n";
	}
}