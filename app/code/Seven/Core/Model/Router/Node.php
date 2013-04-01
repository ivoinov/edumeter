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

	class Core_Model_Router_Node extends Core_Model_Abstract {
	
		public function addPath($id) {
			$path = $this->getPath();
			$path[$id] = $id;
			$this->setPath($path);
			return $this;
		}
		
		public function getPath() {
			$path = parent::getPath();
			if(!is_array($path))
				return array();
			return $path;
		}
		
		public function addArg($key, $value) {
			$args = $this->getArgs();
			$args[$key] = $value;
			$this->setArgs($args);
			return $this;
		}
		
		public function getArg($key) {
			$args = $this->getArgs();
			if(isset($args[$key]))
				return $args[$key];
			return null;
		}
		
		protected $_children = null;
		
		public function getChildren() {
			if(!$this->_children) {
				$this->_children = array();
				foreach((array)$this->getConfig('routes') as $route => $config) {
					$this->_children[$route] = Seven::getModel('core/router_node', array(
						'config' => $config,
						'id' => $this->getId() . "/" . $route,
						'route' => $this->getRoute() . "/" . $route,
					));
				}
			}
			return $this->_children;
		}
		
		public function getChild($name) {
			$children = $this->getChildren();
			if(isset($children[$name]))
				return $children[$name];
			return null;
		}
		
		public function getConfig($key = null) {
			$config = parent::getConfig();
			if($key === null)
				return $config;
			return isset($config[$key]) ? $config[$key] : null;
		}
		
		public function getActionConfig($key = null) {
			$child = $this->getChild($this->getAction());
			if(!$child)
				return null;
			$config = $child->getConfig();
			if($key === null)
				return $config;
			return isset($config[$key]) ? $config[$key] : null;
		}
		
		public function getControllerInstance() {
			if(!$this->getConfig('controller'))
				return false;
			return Seven::getObjectByAlias($this->getConfig('controller'), 'controller', $this->getConfig());
		}
		
		public function dispatch() {
			if(!($controller = $this->getControllerInstance()))
				throw new Core_Exception_Noroute('There no controller for node ' . $this->getId());
			$controller->setRequest(Seven::app()->getRequest());
			return $controller->dispatch($this->getAction());
		}
		
	}