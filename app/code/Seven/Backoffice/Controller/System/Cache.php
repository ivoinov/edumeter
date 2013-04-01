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
 * @package    Backoffice
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

	class Backoffice_Controller_System_Cache extends Core_Controller_Crud_Read {

		public function indexAction() {
			if(!Seven::cache()->getOption('caching'))
				Seven::getSingleton('core/session')->addWarning(__("The caching is disabled"));
			$this->_listAbstract();
		}
		
		public function flushAction() {
			if($this->getRequest()->getParam('tag') === null)
				throw new Core_Exception_Noroute('You should specify Cache tag in tag parameter');
			$tag = Seven::getModel('core/cache_tag')->load($this->getRequest()->getParam('tag'));
			
			if(!$tag->_getId())
				throw new Core_Exception_Noroute('Cache tag was not found');
				
			try {
				$tag->flush();
				Seven::getSingleton('core/session')->addSuccess(__("Cache was successfully flushed"));
			} catch(Exception $e) {
				Seven::getSingleton('core/session')->addError($e->getMessage());
			}
			
			$this->redirectReferer();
		}
		
		public function flushAllAction() {
			try {
				Seven::cache()->clean();
				Seven::getSingleton('core/session')->addSuccess(__("Cache was successfully flushed"));
			} catch(Exception $e) {
				Seven::getSingleton('core/session')->addError($e->getMessage());
			}
			$this->redirectReferer();
		}
		
	}