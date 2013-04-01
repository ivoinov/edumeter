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

    class Core_Model_Website extends Core_Model_Entity {
                
    	protected $_alias  = "core/website";
    	
        public function getAreasArray() {
            foreach(Seven::getConfig('areas') as $id => $data) {
            	$areas[$id] = isset($data['name']) ? $data['name'] : '?';
            }
        	return $areas;
        }
    
       	public function getConfig($option) {
       		return Seven::getSingleton('core/website_config')->getOption($option, $this->_getId());
       	}
        
        public function getUrl($path = "", $args = array()) {
            return seven_url($this->getCode() . "/" . trim($path, '/'), $args);
        }
        
        protected function _getRoute($request) {
        	$route = explode('/', $request->getRequest());
        	if(reset($route) == $this->getCode()) {
        		unset($route[0]);
        		$route = array_values($route);
        	}
        	return $route;        	
        }

        /**
         * Remove website identity data from request (like website code)
         * to keep routing clean
         * @param Core_Model_Request $request
         */
        
        public function cutRequest(Core_Model_Request $request) {
			$request->setWebsiteRequest(preg_replace('/^' . $this->getCode() . '/', '', $request->getRequest()));
        }
        
        public function load($id, $key = null) {
        	if($id instanceof Core_Model_Request) {
        		$request = explode('/', $id->getRequest());
        		$website_code = reset($request);
        		$website = $this->load($website_code, 'code');
        		if(!$website->_getId()) {
        			$website = $this->load('', 'code');
        		}
        		return $website;
        	}
        	return parent::load($id, $key);
        }
        
        public function getLocale() {
        	$locale = parent::getLocale();
        	if($locale === null) {
        		$language = $this->getConfig('general/site/language');
        		debug()->open("Load website locale '{$language}'");
        			$locale = Seven::getModel('core/locale')
        						->load($language);
        		$this->setLocale($locale);
        	}
        	return $locale;
        }
            
		public function getPublicData() {
        	return array('id' => $this->_getId(), 'code' => $this->getCode());
        }        
    }