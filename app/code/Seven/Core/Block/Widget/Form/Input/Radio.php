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

class Core_Block_Widget_Form_Input_Radio extends Core_Block_Widget_Form_Input_Abstract {
	
	public function getHtmlAttributes() {
		$attributes = parent::getHtmlAttributes();
                $attributes['type'] = "radio";
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
		$inputs_html = array();                
                $attributes = $this->getHtmlAttributes();
		foreach($options as $key => $value){
                    $attributes['value'] = $key;                    
                    $attributes['id'] = $this->_getRadioHtmlId();                    
                    $inputs_html[] = "<input ". $this->getAttributeString($attributes). ((string)$this->getValue() == (string)$key ? ' checked = checked' : "") ." />"."<label for=".$attributes['id'].">".$value."</label>" ;
                }
		return $inputs_html;
	}
                        
        protected function _toHtml() {
            return '<div id = "'.$this->getHtmlId().'">'.implode('<br/>', $this->_getSelectOptions()).'</div>';
	}
        
        protected function _getRadioHtmlId() {
            $html_id = "id" . Core_Block_Abstract::$incremental_id++;
            return $html_id;
        }
               
}