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

class Core_Controller_Deprecated_Crud extends Core_Controller_Deprecated_Crud_Abstract {
	
	public function editAction() {
		if(Seven::app()->getRequest()->getParam('id') === NULL)
			return $this->forward('*/noroute');
		return $this->_renderForm();
	}
	
	public function addAction() {
		if(Seven::app()->getRequest()->getParam('id') !== NULL)
			return $this->forward('*/noroute');
		return $this->_renderForm();
	}
	
	public function deleteAction() {
		$this->_deleteAction();
	}
	
	public function indexAction() {
		$this->_renderList();		
	}
	
}

