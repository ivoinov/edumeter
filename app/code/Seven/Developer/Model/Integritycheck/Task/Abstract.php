<?php 

	abstract class Developer_Model_Integritycheck_Task_Abstract extends Core_Model_Abstract {
		
		/**
		 * 
		 * @param mixed $context
		 * @return Developer_Model_Integritycheck_Report
		 */
		
		abstract public function check($context = null);
		
		protected function _fix($type) {
			return Seven::getModel('developer/integritycheck_report_fix_' . $type);
		}
	
		protected function _report($message = null) {
			return Seven::getModel('developer/integritycheck_report')
						->setMessage($message);
		}
		
		protected function _reportError($message = null) {
			return Seven::getModel('developer/integritycheck_report')
						->setMessage($message)
						->setState(Developer_Model_Integritycheck_Report::STATE_ERROR);
		}
		
		protected function _reportWarning($message = null) {
			return Seven::getModel('developer/integritycheck_report')
						->setMessage($message)
						->setState(Developer_Model_Integritycheck_Report::STATE_WARNING);
		}
		
		protected function _reportInfo($message = null) {
			return Seven::getModel('developer/integritycheck_report')
						->setMessage($message)
						->setState(Developer_Model_Integritycheck_Report::STATE_INFO);
		}
		
		protected function _reportAdvice($message = null) {
			return Seven::getModel('developer/integritycheck_report')
						->setMessage($message)
						->setState(Developer_Model_Integritycheck_Report::STATE_ADVICE);
		}
		
	}