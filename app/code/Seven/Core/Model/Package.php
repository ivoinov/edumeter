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

class Core_Model_Package extends Core_Model_Entity {
	
	protected $_alias = "core/package";
	
	public function scanNewPackages() {
		$this->_getResource()->scanNewPackages();
	}
	
	public function getBasePath() {
		return BP . DS . "app" . DS . "code" . DS . $this->getPool() . DS . $this->getSystemName();
	}
	
	/**
	 * Get package data from XML config file
	 */
	
	public function loadFromConfig() {
		$config = $this->getConfig();
		$data = $config->getOption('modules/' . $this->_getId());
		if(! isset($data['version']))
			throw new Exception('Package version not specified');
		$this->addData($data);
		return $this;
	}
	
	public function _setId($id = false) {
		parent::_setId($id);
		list($pool, $sysname) = (strpos($this->_getId(), '_') === false) ? array('Seven', $this->_getId()) : explode('_', $this->_getId(), 2);
		$this->setPool($pool);
		$this->setSystemName($sysname);
		return $this;
	}
	
	protected $_config;
	
	public function getConfig() {
		if(! $this->_config)
			$this->_config = Seven::getModel('core/package_config')->setPackage($this);
		return $this->_config;
	}
	
	public function enable() {
		$this->_getResource()->enable($this);
		return $this;
	}
	
	public function disable() {
		$this->_getResource()->disable($this);
		return $this;
	}
	
	public function install() {
		$this->_getResource()->install($this);
		return $this;
	}
	
	public function upgrade() {
		$this->_getResource()->install($this);
		return $this;
	}   
	
	public function isUpgradeAvailabel() {
		return $this->_getResource()->isUpgradeAvailabel($this);
	}
	
}
