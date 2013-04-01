<?php 

	class Developer_Model_Integritycheck extends Developer_Model_Integritycheck_Task_Abstract {
		
		/**
		 * @see Developer_Model_Integritycheck_Abstract::check()
		 */
		
		public function check($context = null) {
			$report = Seven::getModel('developer/integritycheck_report')
						->setMessage(__('System integrity checker'));
						
			if($checkers = Seven::getConfig('integrity_check', 'global')) {
				foreach($checkers as $key => $checker)
					$report->addSubreport($key, Seven::getModel($checker)->check());
			}
			
			return $report;
		}
		
	}