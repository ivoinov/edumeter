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
 * @package    News
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

	class News_Resource_Entity extends Core_Resource_Multiview_Entity {
		
		public function init(&$object) {
			parent::init($object);
			$user_id = Seven::getSingleton('users/session') ? (int)Seven::getSingleton('users/session')->getUserId() : 0;
			$object->addData(array(
				'date' => date('Y-m-d'),
				'author' => $user_id
			));
			return $object;
		}
		
	}