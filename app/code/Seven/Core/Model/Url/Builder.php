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

	class Core_Model_Url_Builder extends Core_Model_Url {
		
		/**
		 * Build URL by pattern
		 * 
		 * @param string $pattern contain three URL components: sitecode / controller_path / controller_request
		 *                  NOTES:
		 * 					   1. each component can be replaced to "*" for use current value of component
		 * 						  for example "* /a/b" means: current site, cotronller a action b
		 * 						  			  "* /* /index" means current site, current controller, action index
		 *   
		 * 					   2. The controller_path can contain it own /, so all text before first / it's
		 * 						  a website code, and all text after last / it's an action name. Due this
		 * 						  condition:  a/b/c/, in this example action is not defined (default), but 
		 * 						  in case of missed last / (a/b/c), the action name will be c. For this 
		 * 						  pattern: a/b website code will be "a", action is "b" and controller is root
		 * 
		 * @param array $args query string arguments
		 */
		
		public function build($pattern, $args = array()) {
			if($pattern && $pattern{0} == ':')
				return $this->build($this->_getPatternByAlias(substr($pattern, 1)), $args);
			
			if(strpos($pattern, '?') !== false) {
				list($pattern, $query_string) = explode('?', $pattern, 2);
				$parameters = array();
				parse_str($query_string, $parameters);
				if(isset($parameters['*'])) {
					unset($parameters['*']);
					$parameters += Seven::app()->getRequest()->getParameters();
				}
				$args += $parameters;
			}
			$components = explode('/', $pattern);
			$this->setWebsiteCode($components[0]);
			unset($components[0]);
			$this->setAction(end($components));
			unset($components[key($components)]);
			foreach($components as $index => $component) {
				if(preg_match('/^__([a-z_]+)__$/', $component, $match)) {
					$components[$index] = isset($args[$match[1]]) ? $args[$match[1]] : "";
					unset($args[$match[1]]);
				}
			}
			$this->setController(implode('/', $components));
			$request = "";
			foreach(array('website_code', 'controller', 'action') as $part) {
				if(!$this->getData($part)) continue;
				$request .= "/" . $this->getData($part);
			}
			$this->parse(ltrim($request, '/'), $args);
			return $this;
		}
		
		protected function _getPatternByAlias($alias) {
			if($pattern = Seven::getConfig('urlaliases/' . $alias))
				return $pattern;
			Seven::log(E_WARNING, 'Url alias \'' . $alias . '\' not defined');
			return '*/';
		}
		
		public function setWebsiteCode($code) {
			if($code == '*')
				$code = Seven::app()->getWebsite()->getCode();
			return parent::setWebsiteCode($code);
		}
		
		
		public function setController($path) {
			if($path == '*')
				$path = Seven::app()->getRequest()->getController();
			return parent::setController($path);
		}
		
		
		public function setAction($action) {
			if($action == '*')
				$action = Seven::app()->getRequest()->getAction();
			return parent::setAction($action);
		}
		
	}