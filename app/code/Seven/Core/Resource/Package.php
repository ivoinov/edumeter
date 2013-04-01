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

class Core_Resource_Package extends Core_Resource_Entity {

	protected $_table = "core_package"; 
	protected $_key   = "id";
	protected $_alias = "core/package";
	
	public function init(&$object) {
		return $object;
	}
	
	public function getAttributes() {
		return array(
			'depends' => Seven::getModel('core/entity_attribute_collection', array('serializable' => 1))
		);
	}
	
	public function scanNewPackages() {
		$disabled_packages_hash = (array) Seven::getConfig('disabled_packages', 'system');
		$packages_availability = array();
		foreach($this->getConnection()->select($this->getTable(), array('id'))->fetchCol() as $id)
			$packages_availability[$id] = false;
		
		$pools = glob(BP . DS . "app" . DS . "code" . DS . "*");
		foreach($pools as $pool_path) {
			$pool = basename($pool_path);
			if(! is_dir($pool_path))
				continue;
			$packages = glob($pool_path . DS . "*");
			foreach($packages as $package_path) {
				$package_name = basename($package_path);
				if(! is_dir($package_path))
					continue;
				try {
					$id = ($pool != 'Seven') ? ($pool . '_' . $package_name) : $package_name;
					if(array_key_exists($id, $disabled_packages_hash) || 
					    array_key_exists(($pool == 'Seven') ? ('Seven_' . $id) : $id, $disabled_packages_hash))
					    continue;
					
					$package = Seven::getModel('core/package')->load($id);
					if(! $package->_getId()) { // new package
						$package->_setId($id);
						$package->_setNew();
						$package->setName($package->getSystemName());
					}
					$package->loadFromConfig()->setIndex(9999)->setAvailabel(1)->save();
					$packages_availability[$id] = true;
				} catch(Exception $e) {
					Seven::log(E_WARNING, 'Can\'t update package information ' . $package_name . ': ' . $e);					
				}
			}
		}
		$this->_updateAvailability($packages_availability);
		$this->reindex();
		return $this;
	}
	
	protected function _updateAvailability($packages_availability) {
		$keys = array_keys(array_filter($packages_availability));
		Seven::getDatabaseAdapter()->update($this->getTable(), array('availabel' => 1), array('id' => array('in' => $keys)));
		Seven::getDatabaseAdapter()->update($this->getTable(), array('availabel' => 0), array('id' => array('nin' => $keys)));
	}
	
	public function reindex() {
		$packages = new Seven_Collection();
		// prepare
		$packages_raw = $this->getCollection()->getAvailabel()->load();
		foreach($packages_raw as $package) {
			$package->setIndex(9999);
			$packages[$package->getPool() . '_' . $package->getSystemName()] = $package;
		}
		// add packages one by one
		$top_index = $index = 4999;
		foreach($packages as $package) {
			if($package->getIndex() > $index)
				$package->setIndex($index++);
			$this->_updateDependsOrder($package, $packages, $top_index);
		} 
		// setup order values
		foreach($packages as $package) {
			$package->save();
		}
		return $this;
	}
	
	protected function _updateDependsOrder($package, $packages, &$top_index) {
		foreach((array)$package->getDepends() as $package_id => $version) {
			if(!isset($packages[$package_id]) || $packages[$package_id]->getIndex() < $top_index) continue;
			$packages[$package_id]->setIndex(--$top_index); // add dependence package to the top of the index
			$this->_updateDependsOrder($packages[$package_id], $packages, $top_index);
		}
	} 
	
	public function enable($package) {
		$transaction = $this->getConnection()->inTransaction() ? null : $this->getConnection()->beginTransaction();
		try {
			// 1. Update package info
			$package->loadFromConfig($package->_getId());
			// 2. Check depends
			// TODO: Implement depends
			// 3. Check sources
			if(! file_exists($package->getBasePath()))
				throw new Exception(__('Package sources do not exist'));
			
			// 4. Run installers
			$this->install($package);
			// 5. Update package status
			$package->setActive(1)->save();
			if($transaction)
				$transaction->commit();
		} catch(Exception $e) {
			if($transaction)
				$transaction->rollback();
			throw $e;
		}
		return $this;
	}
	
	public function disable($package) {
		$transaction = $this->getConnection()->inTransaction() ? null : $this->getConnection()->beginTransaction();
		try {
			// 1. Update package info
			// 2. Check depends
			// 3. Run uninstallers 
			// 4. Update package status
			$package->setActive(0)->save();
			if($transaction)
				$transaction->commit();
		} catch(Exception $e) {
			if($transaction)
				$transaction->rollback();
			throw $e;
		}
		return $this;
	}
	
	protected function _getAvailabelInstaller($package) {
		$installer = clone Seven::getResource('core/package_setup');
		$installer->setData(array('version' => '0.0.0', 'path' => false));
		
		if(!$package->getInstalledVersion())
			$installers = glob($package->getBasePath() . DS . "install" . DS . "install-*.php");
		else
			$installers = glob($package->getBasePath() . DS . "install" . DS . "upgrade-" . $package->getInstalledVersion() . "-*.php");
		
		if(! $installers)
			return false;
		
		foreach($installers as $entry) {
			if(preg_match("/([0-9]+\.[0-9]+\.[0-9])\.php\$/", basename($entry), $match)) {
				if(version_compare($match[1], $installer->getVersion()) > 0) {
					$installer->setData(array('version' => $match[1], 'path' => $entry));
				}
			}
		}
		if($installer->getPath())
			return $installer;
		return false;
	}
	
	public function install($package) {
		$installer = $this->_getAvailabelInstaller($package);
		if($installer) {
			try {
				$installer->run();
				$package->setInstalledVersion($installer->getVersion())->save();
				Seven::log(E_NOTICE, "Installed " . $package->_getId() . " version " . $installer->getVersion());
			} catch(Exception $e) {
				Seven::log(E_WARNING, "Can't install " . $package->_getId() . " version " . $installer->getVersion());
				throw $e;
			}
			// Try to apply next upgrade
			if(version_compare($package->getInstalledVersion(), $package->getVersion()) != 0)
				$this->install($package);
		}
	}
	
	public function isUpgradeAvailabel($package) {
		return (bool)$this->_getAvailabelInstaller($package);
	}

}