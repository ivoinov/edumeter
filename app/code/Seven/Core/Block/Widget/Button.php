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

class Core_Block_Widget_Button extends Core_Block_Abstract {
	
	public function getHtmlAttributes() {
		$attributes = array_merge(parent::getHtmlAttributes(), array('id' => $this->getHtmlId(), 'value' => $this->getLabel(), 'type' => $this->hasType() ? $this->getType() : "button"));
		
		if($this->getHref())
			$attributes['onclick'] = 'document.location = "' . $this->getHref() . '";return false;';
		if($this->getDisabled() || !$this->isAllowed())
			$attributes['disabled'] = 'disabled';
		if($this->getHtmlName())
			$attributes['name'] = $this->getHtmlName();
		
		return $attributes;
	}

	public function getHref() {
		if($this->hasHref())
			return parent::getHref();
		if($this->hasData('url'))
			return seven_url($this->getData('url'));
		if($this->hasLocation())
			return seven_url($this->getLocation());
		return null;
	} 
		
	protected function _toHtml() {
		return "<input " . $this->getAttributeString() . ">";
	}

	public function isAllowed() {
		if(!($this->getHref() instanceof Core_Model_Url))
			return true;
		$permissions = new Seven_Object(array('allowed' => null, 'route' => $this->getHref()));
		Seven::app()->event('route_access', $permissions);
		return $permissions->getAllowed() || $permissions->getAllowed() === null;
	}
	
}