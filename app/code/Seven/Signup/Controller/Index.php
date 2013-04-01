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
 * @package    Signup
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

	class Signup_Controller_Index extends Core_Controller_Crud_Abstract {
				
		public function indexAction() {
			$this->_createAbstract(array(
				'create_message' 	=> __("Your account has been successfully created"),
				'create_redirect' 	=> seven_url(':login'),
				'create_form_init' 	=> 'signup/user'
			));
		}
		
	}