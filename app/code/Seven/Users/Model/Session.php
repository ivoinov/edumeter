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
 * @package    Users
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Users_Model_Session extends Core_Model_Abstract {

	public function login($data) {
		if(isset($data['email']) && isset($data['password'])) {
			$user = Seven::getModel('users/user')->load($data['email'], 'email');
			if($user->_getId() && Seven::getHelper('core/encrypt')->campare($user->getPassword(), $data['password'])) {
				$this->setUser($user);
				return true;
			}	
		}
		return false;
	}
	
	public function isLoggedIn() {
		return (bool)$this->getUser()->_getId();
	}
	
	public function logout() {
		$this->setUser(false);
	}
	
	protected function setUser($user) {
		if(!$user || !$user->_getId()) {
			$this->_getSession()->setUserId(0);
			parent::setUser(Seven::getModel('users/user')->setData((array)Seven::getConfig('users/guest_profile')));
		} else {
			$this->_getSession()->setUserId($user->_getId());
			parent::setUser($user);
		}
		return $this;
	}
	
	public function getUser() {
		if(!parent::getUser())
			$this->setUser(Seven::getModel('users/user')->load($this->_getSession()->getUserId()));
		return parent::getUser();
	}
	
	protected function _getSession() {
		return Seven::getSingleton('core/session');
	}
	
}