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
 * @package    Access
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Access_Resource_Role extends Core_Resource_Entity {

	public function save(&$object) {
		parent::save($object);
		$this->_savePermissions($object);
	}
	
	public function loadPermissions($object) {
		return $this->_loadPermissions($object);
	}
	
	protected function _loadPermissions($object) {
		$object->setPermissions(
			$this->getConnection()
			->select($this->getPermissionsTable(), array('uri', 'access'))
			->filter('role_id', $object->_getId())
			->fetchPairs()
		);
	}
	
	protected function _savePermissions($object) {
		$permissions = $object->getData('permissions');
		if(empty($permissions)) return $this;
		foreach($permissions as $uri => $access) {
			if($access === 'allow' || $access === true)
				Seven::getDatabaseAdapter()->place($this->getPermissionsTable(), array('uri' => $uri, 'role_id' => $object->_getId(), 'access' => 'allow'));
			else if($access === 'deny' || $access === false)
				Seven::getDatabaseAdapter()->place($this->getPermissionsTable(), array('uri' => $uri, 'role_id' => $object->_getId(), 'access' => 'deny'));
			else 
				Seven::getDatabaseAdapter()->delete($this->getPermissionsTable(), array('uri' => $uri, 'role_id' => $object->_getId()));
		}
		return $this;
	} 
	
	protected $_permission_table;
	
	public function getPermissionsTable() {
		if($this->_permission_table === NULL) {
			$this->_permission_table = Seven::getConfig("entity/" . $this->getAlias() . "/permission_table");
			if(empty($this->_permission_table))
				throw new Exception("Permission table for " . get_class($this) . " not specified in XML");
		}
		return $this->_permission_table;
	}
	
	protected function _afterLoad(&$object) {
		$this->_loadPermissions($object);
		parent::_afterLoad($object);
	}
	
}