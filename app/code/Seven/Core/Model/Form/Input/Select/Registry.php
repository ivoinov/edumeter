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
 * @package    Core
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

    class Core_Model_Form_Input_Select_Registry  {
        
        public function getOptionsArray($owner) {
        	if(!$owner || !$owner->getRegistryName())
        		throw new Core_Exception_Warning('Owner not passed to the function');
        	if(strpos($owner->getRegistryName(), '::') === false) {
        		return (array)Seven::registry($owner->getRegistryName());
        	}
        	list($registry, $method) = explode("::", $owner->getRegistryName(), 2);
        	return (array)call_user_func(array(Seven::registry($registry), $method));
        }
        
    }