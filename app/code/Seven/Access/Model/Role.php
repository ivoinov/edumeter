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

class Access_Model_Role extends Core_Model_Entity {
	
	public function load($id, $id_field = NULL) {
		return $this->_getResource()->load($this, $id, $id_field);
	}
	
	protected $_options = null;
	
	public function getOptionsArray() {
		if($this->_options === null) {
			$this->_options = array();
			foreach($this->getCollection()->load() as $role) {
				$this->_options[$role->_getId()] = $role->getName();
			}
		}
		return $this->_options;
	}
	
	public function getPermissions() {
		if(($permissions = parent::getPermissions()) === NULL) {
			$this->_getResource()->loadPermissions($this);
			return parent::getPermissions();
		}
		return $permissions;
	}
	
	public function isAllowed($uri) {
		$permissions = $this->getPermissions();
		$allowed = isset($permissions['/']) ? ($permissions['/'] == 'allow') : false;
		$_uri = "";
		foreach(explode('/', trim($uri, '/')) as $part) {
			$_uri .= '/' . $part;
			if(isset($permissions[$_uri]))
				$allowed = $permissions[$_uri] == 'allow'; 
		}
		return $allowed;
	}
	
}