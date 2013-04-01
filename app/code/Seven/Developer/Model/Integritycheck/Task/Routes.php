<?php 

	class Developer_Model_Integritycheck_Task_Routes extends Developer_Model_Integritycheck_Task_Abstract {
		
		public function check($context = null) {
			$report = Seven::getModel('developer/integritycheck_report')
							->setMessage(__('Routes'));
			foreach($this->_getAreas() as $area) {
				$report->addSubreport($area, $this->_checkAreaRoutes($area));
			}
			return $report;
		}

		protected function _getAreas() {
			return array_keys(Seven::getConfig('areas', 'global'));
		}
		
		protected function _checkAreaRoutes($area) {
			return $this->_checkRoutes('/', Seven::getConfig('routing', $area))->setMessage(__('Routes of %s', $area));
		}
		
		protected function _checkRoutes($routename, $routedata) {
			$report = $this->_report($routename);
			
			foreach(array('_checkRouteController', '_checkRouteAccess', '_checkRouteType', '_checkRouteSubroutes', '_checkRouteActions') as $checker) {
				if($c_report = call_user_func(array($this, $checker), $routedata))
					$report->addSubreport($checker, $c_report);
			}
				
			return $report;
		}
		
		protected function _checkRouteSubroutes($data) {
			if(!isset($data['routes']))
				return null;
			$routereport = $this->_report(__('<b>Subroutes</b>'));
			foreach($data['routes'] as $routename => $routedata) {
				if(isset($routedata['type']) && $routedata['type'] == 'action')
					continue;
				$routereport->addSubreport($routename, $this->_checkRoutes($routename, $routedata));
			}
			if($routereport->getSubreports())
				return $routereport;			
		}
		
		protected function _checkRouteController($data) { 
			if(!isset($data['controller'])) 
				return null;
			$class_name = Seven::getClassByAlias($data['controller'], 'controller');
			$report = $this->_report(__("<b>Controller:</b> %s &rarr; %s", $data['controller'], $class_name));
			if(!class_exists($class_name)) {
				$report->setState(Developer_Model_Integritycheck_Report::STATE_WARNING);
			} else if(preg_grep('/^Core_Controller_Crud_Abstract/', class_parents($class_name))) {
				$report->addSubreport('is_curd', $this->_report(__('This is CRUD controller')));
				$report->addSubreport('use', $this->_report(__("Controller use %s entity", isset($data['use']) ? $data['use'] : "not defined")));
			}
			return $report;
		}
		
		protected function _checkRouteActions($data) {
			if(!isset($data['controller'])) 
				return null;
			$class_name = Seven::getClassByAlias($data['controller'], 'controller');
			if(!class_exists($class_name))
				return null;
			
			$report = $this->_report(__("<b>Actions</b>"));
			$reflection = new ReflectionClass($class_name);
			$actions = array();
			foreach($reflection->getMethods() as $method) {
				/** @var $method ReflectionMethod */
				if(preg_match('/^(.+)Action$/', $method->getName(), $m) && $method->isPublic())
					$actions[$m[1]] = $m[1];
			}
			foreach($actions as $action) {
				$report->addSubreport($action, $this->_report($action));
			}
			return $report;
		}
		
		protected function _checkRouteType($data) {
			if(!isset($data['type']))
				return null;
			return $this->_report(__("<b>Type:</b> %s", $data['type']));
		}
		
		protected function _checkRouteAccess() {
			if(!isset($data['access']))
				return null;
			return $this->_report(__("<b>Access:</b> %s", $data['access']));
			
		}
		
	}