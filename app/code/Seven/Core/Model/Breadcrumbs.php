<?php 

	class Core_Model_Breadcrumbs extends Core_Model_Abstract {
		
		protected $_crumbs = array();
		
		public function getCrumbs() {
			return $this->_crumbs;
		}
		
		public function addCrumb($id, $data) {
			$this->_crumbs[$id] = new Seven_Object($data);
			return $this;
		}
		
		public function setCrumb($id, $key, $value = null) {
			if(isset($this->_crumbs[$id])) {
				if($value === null && is_array($key))
					$this->_crumbs[$id]->addData($key);
				else 
					$this->_crumbs[$id]->addData($key, $value);
			}
			return $this;	
		}
		
		public function removeCrumb($id) {
			unset($this->_crumbs[$id]);
			return $this;
		}
		
		public function getCrumb($id) {
			if(isset($this->_crumbs[$id]))
				return $this->_crumbs[$id];
			return null;
		} 
		
		public function __construct($data = array()) {
			$this->init();
			parent::__construct($data);
		}
		
		public function init() {
			$this->addCrumb('~', array('name' => __('Breadcrumb::Home'), 'url' => seven_url('*')));
			if($node = Seven::app()->getRequest()->getRouteNode()) {
				$urlpath 	= array('*');
				$configpath	= array('routing');
				$args 		= $node->getArgs();
				
				foreach($node->getPath() as $id) {
					$path = preg_match('/^__([a-z_]+)__$/', $id, $m) ? (isset($args[$m[1]]) ? $args[$m[1]] : $m[0]) : $id;
					$urlpath[] 		= $path;
					$configpath[] 	= "routes/" . $id;
					
					$url 			= seven_url(implode('/', $urlpath) . "/");
					$config 		= new Seven_Object(Seven::getConfig(implode('/', $configpath)) ?: array());
					
					$this->addCrumb($id, array('name' => $config->getCrumbName() ?: ($config->getName() ?: $path), 'url' => $url));
				}
			}
			return $this;
		}
		
	}