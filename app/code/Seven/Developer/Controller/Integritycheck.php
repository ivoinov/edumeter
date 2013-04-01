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
 * @package    Developer
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

    class Developer_Controller_Integritycheck extends Core_Controller_Crud_Abstract {

    	protected function _prepareView($options = array()) {
    		$options = $this->_extendControllerOptions($options);
    		$entity = Seven::getSingleton('developer/integritycheck')->check();
    		Seven::register($options->getViewRegistryName(), $entity);
    		return $entity;
    	}
    	
        public function indexAction() {
        	$this->getLayout()->addTag('developer_integritycheck_page');
            $this->_viewAbstract();
        }
    }