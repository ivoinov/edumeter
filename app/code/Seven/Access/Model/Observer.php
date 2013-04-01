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

class Access_Model_Observer {

	public function isRouteAllowed($observer) {
		if($observer->getAllowed() !== null)
			return;
			
		if($observer->getRoute() instanceof Core_Model_Url)
			$observer->setAllowed(Seven::getSingleton('access/session')->isAllowedUrl(
				$observer->getRoute()
			)); 
		else
			$observer->setAllowed(Seven::getSingleton('access/session')->isAllowed(
				trim($observer->getRoute() . '/' . $observer->getAction(), '/'),
				$observer->getArea()
			)); 
	}
	
	public function deniedAction($observer) {
		if(!Seven::getSingleton('users/session')->isLoggedIn())
			throw new Core_Exception_Redirect(seven_url(':login'), 302);
		if(Seven::getSingleton('access/session')->isAllowedUrl(seven_url("*/")))
			return;
		if(Seven::app()->getWebsite()->getCode() && Seven::getConfig('areas/' . Seven::app()->getWebsite()->getArea() . '/secure')) 
			throw new Core_Exception_Forward(seven_url('/denied'));
	}

	public function norouteAction($observer) {
		if(Seven::getSingleton('access/session')->isAllowedUrl(seven_url("*/")))
			return;
		if(Seven::app()->getWebsite()->getCode() && Seven::getConfig('areas/' . Seven::app()->getWebsite()->getArea() . '/secure')) {
			throw new Core_Exception_Forward(seven_url('/noroute'));
		}
	}
	
}