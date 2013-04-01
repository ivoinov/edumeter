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

class Users_Controller_Account extends Users_Controller_Account_Abstract {

	public function loginAction() {
		return $this->_loginAbstract();
	}

	public function logoutAction() {
		return $this->_logoutAbstract();
	}
	
	public function indexAction() {
		return $this->_editAbstract();
	}
	
}
