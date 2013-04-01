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
 * @package    Libs
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */
	
	class Seven_Db extends Zend_Db {
		
		static public function factory($adapter, $config = array()) {
			if($config instanceof Seven_Object) {
				$config->setData('adapterNamespace', 'Seven_Db_Adapter');
				$config = new Zend_Config($config->getData());
			}
			return parent::factory($adapter, $config);
		}
		
	}