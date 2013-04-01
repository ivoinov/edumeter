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

class Access_Model_Session extends Core_Model_Abstract {

	/**
	 * @param string $access_uri it's not an URL, just a path to the resource
	 */
	
	public function isAllowed($access_uri) {
		return $this->getRole()->isAllowed($access_uri);
	}
	
	protected function _isValidWebsiteCode($code) {
		return Seven::getResource('core/website')->isValidCode($code);
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
			case 'GUESTONLY': 	return !Seven::getSingleton('users/session')->isLoggedIn();
			case 'USERONLY': 	return Seven::getSingleton('users/session')->isLoggedIn();
		}	
		return $this->isAllowed($node->getWebsite()->getArea() . "/" . $node->getId() . '/' . $node->getAction());
	}
	
	protected $_role;
	
	public function getRole() {
		if($this->_role === null) {
			$this->_role = Seven::getModel('access/role')->load((int)Seven::getSingleton('users/session')->getUser()->getRoleId());
		}
		return $this->_role;
	}
	
}