<?php 

	class Developer_Model_Integritycheck_Task_Permissions extends Developer_Model_Integritycheck_Task_Abstract {
		
		public function check($context = null) {
			$report = Seven::getModel('developer/integritycheck_report')
							->setMessage(__('Directory Permissions'));
			foreach($this->_getWebOwnedDirictories() as $path) {
				$report->addSubreport($path, $this->_checkPermissions($path));
			}
			return $report;
		}

		protected function _checkPermissions($path) {
			$report = Seven::getModel('developer/integritycheck_report');
			if(is_writable(BP . DS . $path)) {
				$report->setState(Developer_Model_Integritycheck_Report::STATE_OK)
					->setMessage('Directory ' . $path . ' are writable');
			} else {
				$report->setState(Developer_Model_Integritycheck_Report::STATE_ERROR)
					->setHowtoFix($this->_fix('terminal')->setCommand('chmod 0777 -R "' . BP . DS . $path . '"'))
					->setMessage('Directory ' . $path . ' are NOT writable');
			}	
			return $report;
		}
		
		protected function _getWebOwnedDirictories() {
			return array('var', 'public/media');
		}
		
	}