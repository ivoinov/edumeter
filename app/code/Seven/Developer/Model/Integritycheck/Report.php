<?php 

/**
 * 
 * @method string getName()
 * @method string getState()
 * @method string getHowtoFix();
 * @method string getMessage();
 * @method Developer_Model_Integritycheck_Report setName(string $name)
 * @method Developer_Model_Integritycheck_Report setHowtoFix(string $howto)
 * @method Developer_Model_Integritycheck_Report setState(string $state)
 * @method Developer_Model_Integritycheck_Report setMessage(string $message)
 *
 */

	class Developer_Model_Integritycheck_Report extends Core_Model_Abstract {
		
		const STATE_INFO 	= 1;
		const STATE_OK 		= 2;
		const STATE_ADVICE 	= 3;
		const STATE_WARNING	= 4;
		const STATE_ERROR 	= 5;
		
		public function __construct($data = array()) {
			if(empty($data['state']))
				$this->setState(self::STATE_OK);
		} 
		
		protected $_subreports = array();
		
		/**
		 * @param string $key
		 * @return Developer_Model_Integritycheck_Report|NULL
		 */
		
		public function getSubreport($key) {
			$reports = $this->getSubreports();
			if(isset($reports[$key]))
				return $reports[$key];
			return null;
		}
		
		/**
		 * @return array
		 */
		
		public function getSubreports() {
			return $this->_subreports;
		}
		
		/**
		 * @param string $key
		 * @param Developer_Model_Integritycheck_Report $subreport
		 * @return Developer_Model_Integritycheck_Report
		 */
		
		public function addSubreport($key, Developer_Model_Integritycheck_Report $subreport) {
			$this->_subreports[$key] = $subreport;
			if($subreport->getState() > $this->getState())
				$this->setState($subreport->getState());
			return $this;
		}
		

		public function addSubreportEx($key, $state, $message, $howtofix = null) {
			return $this->addSubreport($key, Seven::getModel('developer/integritycheck_report')
					->setState($state)
					->setMessage($message)
					->setHowtoFix($howtofix)
			);
		}
		
	}