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
 * @package    Admin
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Admin_Model_Session extends Core_Model_Abstract {

	/**
	 * @param string $access_uri it's not an URL, just a path to the resource
	 */
	
	public function isAllowed($access_uri) {
		return $this->isLoggedIn();
	}
	
	protected function _getGlobalState($uri, $area) {
		$parts = explode('/', $uri);
		$action = array_pop($parts);
		$path   = implode('/', $parts);
		if(!($node = Seven::app()->getWebsite()->getControllerByPath($path)))
			return false;
		$action = $node->getAction($action);
		return $action->getAccess() ? $action->getAccess() : $node->getAccess(); 
	}
	
	public function isAllowedUrl($url) {
		if(!is_object($url))
			$url = seven_url($url);
		$node = Seven::app()->getRouter()->dispatch($url);
		switch(strtoupper($node->getActionConfig('access') ? $node->getActionConfig('access') : $node->getConfig('access'))) {
			case 'ALLOW': 		return true;
			case 'DENY': 		return false; 
			case 'GUESTONLY': 	return !$this->isLoggedIn();
			case 'USERONLY': 	return $this->isLoggedIn();
		}	
		return $this->isAllowed($node->getWebsite()->getArea() . "/" . $node->getId() . '/' . $node->getAction());
	}

	public function login($data) {
		if(isset($data['email']) && isset($data['password'])) {
			$user = Seven::getModel('admin/user')->load($data['email'], 'email');
			if($user->_getId() && Seven::getHelper('core/encrypt')->campare($user->getPassword(), $data['password'])) {
				$this->setAdmin($user);
				return true;
			}	
		}
		return false;
	}
	
	public function isLoggedIn() {
		return (bool)$this->getAdmin()->_getId();
	}
	
	public function logout() {
		$this->setAdmin(false);
	}
	
	protected function setAdmin($user) {
		if(!$user || !$user->_getId()) {
			$this->_getSession()->setAdminId(0);
			parent::setAdmin(Seven::getModel('admin/user')->setData((array)Seven::getConfig('admin/guest_profile')));
		} else {
			$this->_getSession()->setAdminId($user->_getId());
			parent::setAdmin($user);
		}
		return $this;
	}
	
	public function getAdmin() {
		if(!parent::getAdmin())
			$this->setAdmin(Seven::getModel('admin/user')->load($this->_getSession()->getAdminId()));
		return parent::getAdmin();
	}
	
	protected function _getSession() {
		return Seven::getSingleton('core/session');
	}
}