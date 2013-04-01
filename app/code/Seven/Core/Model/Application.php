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

class Core_Model_Application extends Core_Model_Abstract {

	protected $_data;
	
	public function init() {
		debug()->open('Init application');
		
		$this->getRequest()->init();
		
		$this->getResponse()
			->setIsAjax($this->getRequest()->isAjax())
			->init();
			
		debug()->close();
		return $this;
	}

	protected function _run() {
		try {
			$request = $this->getRequest();
			// get routing node
			$node = $this->getRouter()->dispatch($request);
			$this->setRoutingNode($node);
			$this->setWebsite($node->getWebsite());
			// fill request with routing data
			$request->setController($node->getRoute());
			$request->setRouteNode($node);
			$request->setAction($node->getAction());
			$request->setParameters($request->getParameters() + $node->getArgs());
			// dispatch route
			$node->dispatch();
		} catch(Core_Exception_Noroute $e) {
			debug()->log('Catch no route exception ' . $e->getMessage(), E_NOTICE);
			$this->forward("*/noroute");
		} catch(Core_Exception_Forbidden $e) {
			$this->forward("*/forbidden");
		} catch(Core_Exception_Denied $e) {
			$this->forward("*/denied");
		} catch(Core_Exception_Redirect $e) {
			$this->getResponse()->redirect($e->getMessage(), $e->getCode());
		} catch(Core_Exception_Forward $e) {
			$this->forward($e->getMessage());
		}
	}
	
	public function shutdown() {
		debug()->open('Shutdown application');
		$this->getResponse()->send();
		debug()->close();
		return $this;
	}
	
	public function isDeveloperMode() {
		return (bool) Seven::isDeveloperMode();
	}

	public function version($asString = false) {
		static $version = array(
        	'major'     => 1,
            'minor'     => 1,
            'revision'  => 0,
            'patch'     => 0,
            'build'     => 0,
            'stability' => 'dev'
        );

		if($asString) return sprintf("%d.%d.%d%s%s %s", $version['major'], $version['minor'], $version['revision'], $version['patch'] ? ("." . $version['patch']) : "", $version['stability'] ? ("." . $version['stability'] . "") : "", (int)$version['build'] ? " (build: " . $version['build'] . ")" : "");
        return new Seven_Object($version);
	}
	
	/**
	 * @return Core_Model_Router
	 */
	
	public function getRouter() {
		return Seven::getSingleton('core/router');
	}

	/**
	 *
	 * @return Core_Model_Request
	 */

	public function getRequest() {
		$request = parent::getRequest();
		if(empty($request)) {
			$request = Seven::getModel("core/request");
			parent::setRequest($request);
		}
		return $request;
	}

	/**
	 *
	 * @return Core_Model_Response
	 */

	public function getResponse() {
		$response = parent::getResponse();
		if(empty($response)) {
			$response = Seven::getModel("core/response");
			parent::setResponse($response);
		}
		return $response;
	}

	public function event($key, $args = array()) {
		$observers = $this->getConfig("events/{$key}");
		foreach((array)$observers as $observer) {
			if(!($args instanceof Seven_Object)) {
				$args = new Seven_Object($args);
			}
			if($observer)
				call_seven_callback($observer, 'model', $args);
		}
	}

	/**
	 * 
	 * @return Core_Model_Config
	 */
	
	public function getConfigModel() {
		if(($config = parent::getConfigModel()) === null) {
			parent::setConfigModel($config = new Core_Model_Config());
		}
		return $config;
	}

	public function getConfig($option, $area = null) {
		return $this->getConfigModel()->getOption($option, $area);
	}

	public function getSiteConfig($option) {
		if($this->getWebsite())
			return $this->getWebsite()->getConfig($option);
		return Seven::getSingleton('core/website')->_setId(0)->getConfig($option);
	}
	
	public function forward($url) {
		$this->getRequest()->parse(seven_url($url));
		$this->_run();
	}
	
	protected $_website;
	
	public function getWebsite() {
		return $this->_website;
	}
	
	protected function setWebsite($website) {
		$this->_website = $website;
		return $this;
	}
	
	public function run() {
		debug()->open("Application run");
		$this->init();		
		$this->_run();
		$this->shutdown();
		debug()->close();

		// echo debug info
		if($this->isDeveloperMode() && Seven::getConfig("debug/profiler") && !$this->getResponse()->isAjax())
			echo debug()->toHtml();
	}

	protected $_loaded_packages = null;
	
	public function getLoadedPackages() {
		if($this->_loaded_packages === null) {
			// load active packages
			$this->_loaded_packages = (array)Seven::getCollection('core/package')->getActive()->load();
			// check if all system packages loaded
			$system_packages = Seven::getConfig('required_packages', 'system');
			foreach($this->_loaded_packages as $package) {
				unset($system_packages[$package->getPool() . '_' . $package->getSystemName()]);
			}
			if(count($system_packages) !== 0) {
				$checksum = count($this->_loaded_packages) + count($system_packages);
				Seven::getResource('core/package')->scanNewPackages();
				
				foreach($system_packages as $package_id => $state) {
					if(strpos($package_id, 'Seven_') === 0)
						$package_id = substr($package_id, 6);
					try {
						Seven::getModel('core/package')->load($package_id)
							->enable();
					} catch(Exception $e) {
						Seven::log(E_WARNING, 'Can\'t enable system package ' . $package_id . '');
					}
				}
				$this->_loaded_packages = (array)Seven::getCollection('core/package')->getActive()->load();
				if(count($this->_loaded_packages) != $checksum)
					throw new Core_Exception_Error('Some of system required packages are not exists');
			}		
		}
		return $this->_loaded_packages;
	}
	
}
