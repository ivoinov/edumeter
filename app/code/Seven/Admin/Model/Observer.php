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

class Admin_Model_Observer {

	public function isRouteAllowed($observer) {
		$area = $observer->getArea() ?: Seven::app()->getWebsite()->getArea();
		if(Seven::getConfig('areas/' . $area . '/secure')) { // process only secure areas
			if($observer->getRoute() instanceof Core_Model_Url)
				$observer->setAllowed(Seven::getSingleton('admin/session')->isAllowedUrl($observer->getRoute())); 
			else $observer->setAllowed(Seven::getSingleton('admin/session')->isAllowed(trim($observer->getRoute() . '/' . $observer->getAction(), '/'), $observer->getArea()));
		}
	}
	
	public function deniedAction($observer) {
		if(!Seven::getSingleton('admin/session')->isLoggedIn())
			throw new Core_Exception_Redirect(seven_url(':admin_login'), 302);
		if(Seven::app()->getWebsite()->getCode() && Seven::getConfig('areas/' . Seven::app()->getWebsite()->getArea() . '/secure')) 
			throw new Core_Exception_Forward(seven_url('/denied'));
	}

	public function norouteAction($observer) {
		if(!Seven::getSingleton('admin/session')->isLoggedIn())
			if(Seven::app()->getWebsite()->getCode() && Seven::getConfig('areas/' . Seven::app()->getWebsite()->getArea() . '/secure'))
				throw new Core_Exception_Forward(seven_url('/noroute'));
	}
	
}