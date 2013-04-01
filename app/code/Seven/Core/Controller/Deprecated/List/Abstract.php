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
 * @deprecated since 1.0.0 use Core_Controller_Abstract_*
 */

class Core_Controller_Deprecated_List_Abstract extends Core_Controller_Abstract {

	public function getAlias() {
		return $this->getData('use');
	}
	
	public function getGridAlias() {
		if($alias = parent::getGridAlias())
			return $alias;		
		return $this->getAlias();
	}
	
	protected function _renderList() { 
		$this->_prepareList();
		$this->loadLayout();
		if($grid = $this->getLayout()->getBlock('grid')) {
			$this->_beforeGridInit($grid);
			if($this->getGridAlias())
				$grid->initXml($this->getGridAlias());
			$this->_afterGridInit($grid);
//var_dump($grid->getUseAjax(), $this->getGridAlias());
			if($grid->getUseAjax() && $this->getRequest()->getParam('is_ajax')) {
				Seven::app()->getResponse()
					->setIsAjax(true)
					->setBody($grid->prepare()->getInnerHtml());
				return;				
			}
		}
		$this->renderLayout();
	}
	
	protected function _beforeGridInit($grid) {
		return $this;
	} 
	
	protected function _afterGridInit($grid) {
		if(! $grid->getParent()->getTitle())
			$grid->getParent()->setTitle($grid->getTitle() ? $grid->getTitle() : __("List"));
		return $this;
	} 
	
	protected function _prepareList() {
		$this->getLayout()->addTag('default_list_index');
		return $this;
	}
	
}

