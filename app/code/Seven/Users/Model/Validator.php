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

class Users_Model_Validator {

	public function isEmailUnique($value, $field) {
		// if we can get entity and it email didn't changed return true
		if($field->getForm() instanceof Core_Model_Form_Entity && $field->getForm()->getEntity())
			if($value == $field->getForm()->getEntity()->getEmail())
				return true; 
		
		// check e-mail
		if(Seven::getResource('users/user')->isEmailExists($value)) {
			$field->setErrorMessage(__('Account with this e-mail already exists'));
			return false;
		}
		return true;
	}
	
}