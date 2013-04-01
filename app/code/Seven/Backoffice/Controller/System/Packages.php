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

class Backoffice_Controller_System_Packages extends Core_Controller_Crud_Read {
	
	public function getUse() {
		return 'core/package';
	}

	protected function _getCollection($options = array()) {
		return parent::_getCollection($options)->getAvailabel();
	}
	
	public function viewAction() {
		$package_id = $this->getRequest()->getParam('id');
		$package = Seven::getModel('core/package')->load($package_id);
		if(!$package->_getId())
			throw new Core_Exception_Noroute('Package not found');
		Seven::register('view_package', $package);
		$this->loadLayout();
		if($container = $this->getLayout()->getBlock('container')) {
			$container->setTitle($package->getName());
			$helper = Seven::getHelper('backoffice/package');
			if($helper->canBeTurnedOn($package)) {
				$container->addButton('enable', array(
					'label' => __('Enable'),
					'url'	=> seven_url('*/*/enable', array('id' => $package_id))
				));
			} else if($helper->canBeTurnedOff($package)) {
				$container->addButton('disable', array(
					'label' => __('Disable'),
					'url'	=> seven_url('*/*/enable', array('id' => $package_id))
				));
			}
		}
		$this->renderLayout();
	}

	public function refreshAction() {
		Seven::getModel('core/package')->scanNewPackages();
		Seven::cache()->clean();
		$this->redirectReferer();
	}
	
	public function upgradeAllAction() {
		// refresh packages info
		Seven::getModel('core/package')->scanNewPackages();
		Seven::cache()->clean();
		// upgrade all packages
		$upgraded = false;
		foreach(Seven::app()->getLoadedPackages() as $package) {
			try {
				if($package->isUpgradeAvailabel()) {
					$upgraded = true;
					$package->install();
					Seven::getSingleton('core/session')->addSuccess(__('Package %s was upgraded', $package->getName()));
				}
			} catch(Exception $e) {
				Seven::getSingleton('core/session')->addError(__('Can\'t upgrade package: %s', $e->getMessage()));
			}
		}
		if(!$upgraded) {
			Seven::getSingleton('core/session')->addWarning(__('No upgrades available'));
		}
		$this->redirectReferer();
	}
	
	public function upgradeAction() {
		$package_id = $this->getRequest()->getParam('id');
		$package = Seven::getModel('core/package')->load($package_id);
		if(! $package->_getId())
			return $this->forward('*/noroute');
		try {
			$package->install();
			Seven::getSingleton('core/session')->addSuccess(__('Package %s was upgraded', $package->getName()));
		} catch(Exception $e) {
			Seven::getSingleton('core/session')->addError(__('Can\'t upgrade package: %s', $e->getMessage()));
		}
		Seven::cache()->clean();
		$this->redirectReferer();
	}
	
	public function enableAction() {
		$package_id = $this->getRequest()->getParam('id');
		$package = Seven::getModel('core/package')->load($package_id);
		if(! $package->_getId())
			return $this->forward('*/noroute');
		try {
			$package->enable();
			Seven::getSingleton('core/session')->addSuccess(__('Package %s was enabled', $package->getName()));
		} catch(Exception $e) {
			Seven::getSingleton('core/session')->addError(__('Can\'t enable package: %s', $e->getMessage()));
		}
		Seven::cache()->clean();
		$this->redirectReferer();
	}
	
	public function disableAction() {
		$package_id = $this->getRequest()->getParam('id');
		$package = Seven::getModel('core/package')->load($package_id);
		if(! $package->_getId())
			return $this->forward('*/noroute');
		try {
			$package->disable();
			Seven::getSingleton('core/session')->addSuccess(__('Package %s was disabled', $package->getName()));
		} catch(Exception $e) {
			Seven::getSingleton('core/session')->addError(__('Can\'t disable package: %s', $e->getMessage()));
		}
		Seven::cache()->clean();
		$this->redirectReferer();
	}

}