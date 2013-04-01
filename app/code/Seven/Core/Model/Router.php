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

	class Core_Model_Router extends Core_Model_Abstract {
		
		/**
		 * Dispatch any URL and return routing node
		 * @param $request
		 * @return Core_Model_Router_Node
		 */
		
		public function dispatch(Core_Model_Url $request) {
			if(Seven::getConfig('routing') === null)
				throw new Core_Exception_Error('Can\'t found root routing node');
			$path = $request->getRequest();
			list($website_code, $website_request) = explode('/', $path . "/", 2);
			if(!Seven::getResource('core/website')->isValidCode($website_code)) {
				$website_code = '';
				$website_request = $path;
			}
			$website = Seven::getModel('core/website')->load($website_code, 'code');
			$node = $this->getRoutingNode($website_request, $website->getArea());
			$node->setWebsite($website);
			return $node;
		}
		
		public function getRoutingNode($path, $area = null) {
			debug()->open('Routing node ' . $path);
			$node = Seven::getModel('core/router_node', array('args' => array(), 'id' => '', 'route' => ''));
			$path = explode('/', trim($path, '/'));
			$config = $this->_getRoutingConfig($area);
			$action_path = array_reverse($path);
			foreach($path as $id) {
				$route = $id;
								
				if(!isset($config['routes'])) break;
				
				if(!isset($config['routes'][$id])) {
					$variables = preg_grep('/^__(.+)__$/', array_keys($config['routes']));
					$route = null;
					$route_prio = 0; 								
					foreach($variables as $varraw) {
						$variable = substr($varraw, 2, strlen($varraw) - 4);
						if(isset($config['routes'][$varraw]['pattern']) && preg_match($config['routes'][$varraw]['pattern'], $id)) {
							$route = $variable;
							$route_prio = 2; 								
						} else if(!isset($config['routes'][$varraw]['pattern']) && $route_prio <= 1) {
							$route = $variable;
							$route_prio = 1;
						}
					}
					if($route === null) break; 
					$node->addArg($route, $id);
					$id = "__" . $route . "__";
					$route = $node->getArg($route);
				}
				
				$node->addPath($id);
				
				if(isset($config['routes'][$id]['type']) && $config['routes'][$id]['type'] == 'action') {
					if(!empty($config['routes'][$id]['action']))
						$action_path[] = $config['routes'][$id]['action'];
					break;
				}
				
				$node->setRoute($node->getRoute() . "/" . $route);
				$node->setId($node->getId() . "/" . $id);
				$config = $config['routes'][$id];
				array_pop($action_path);
			}
			$action = array_pop($action_path);
			$node->setConfig($config);
			$node->setAction($action ? $action : ($node->getConfig('default_action') ? $node->getConfig('default_action') : "index"));
			$node->setUnmatchedRequest(implode('/', array_reverse($action_path)));
			$node->setId(trim($node->getId(), "/"));
			$node->setRoute(trim($node->getRoute(), "/"));
			debug()->close();
			return $node;
		}
		
		protected function _getRoutingConfig($area = null) {
			if($this->hasConfig())
				return $this->getConfig();
			return Seven::getConfig('routing', $area);
		}
	
	}