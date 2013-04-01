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
 * @package    Static
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Static_Block_Block extends Core_Block_Widget_View {
	
	protected function _getView() {
		if(!$this->getRegistryName() || !($view = parent::_getView()))
			if(!($view = Seven::getModel('static/block')->load($this->getBlockId())))
				if(!($view = $this->getBlock()))
					return Seven::getModel('static/block', array('content' => '')); // return an empty block
		
		if($view instanceof Core_Model_Multiview_Entity)
			$view->_setView($this->getBlockView() ?: Seven::getSingleton('core/session')->getLocale()->getCode());
		
		return $view;
	}
	
	protected function _toHtml() {
		return $this->getView() ? $this->getView()->getContent() : "";	
	}
	
}