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

	class Core_Controller_Index extends Core_Controller_Abstract {
		
        public function norouteAction() {
        	Seven::event('no_route_action');
        	$this->getLayout()->addTag('default_service_noroute');
            Seven::app()->getResponse()->setCode('404');
            $this->render();
        }

        public function forbiddenAction() {
        	Seven::event('forbidden_action');
        	$this->getLayout()->addTag('default_service_forbidden');
        	Seven::app()->getResponse()->setCode('403');
        	$this->render();
        }
		
        public function deniedAction() {
        	Seven::event('denied_action');
        	$this->getLayout()->addTag('default_service_denied');
        	Seven::app()->getResponse()->setCode('403');
        	$this->render();
        }
		
	}