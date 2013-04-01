<?php 

	class Developer_Model_Integritycheck_Task_Entity extends Developer_Model_Integritycheck_Task_Abstract {
		
		public function check($context = null) {
			$report = Seven::getModel('developer/integritycheck_report')
							->setMessage(__('System entities'));
			foreach($this->_getSystemEntities() as $key => $entity) {
				if($entity->getSkipIntegritycheck())
					continue;
				$report->addSubreport($key, $this->_checkEntity($entity));
			}
			return $report;
		}

		protected function _checkEntity($entity) {
			$report = Seven::getModel('developer/integritycheck_report')
							->setMessage($entity->_getId());
			
			$report->addSubreport('classes', Seven::getModel('developer/integritycheck_task_entity_classes')->check($entity));			
			$report->addSubreport('tables', Seven::getModel('developer/integritycheck_task_entity_tables')->check($entity));			
			return $report;
		}
		
		protected function _getSystemEntities() {
			return Seven::getCollection('developer/config_entity');
		}
		
	}