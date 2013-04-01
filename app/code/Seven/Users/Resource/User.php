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

class Users_Resource_User extends Core_Resource_Entity {

	public function isEmailExists($email) {
		$result = Seven::getDatabaseAdapter()
			->select($this->getTable())
			->filter('email', $email)
			->columns(array('count' => 'count(*)'))
			->load();
		if(isset($result[0]['count']))
			return (bool)$result[0]['count'];
		return false;
	}

}
