<?php

	class Developer_Model_Integritycheck_Report_Fix_Terminal extends Developer_Model_Integritycheck_Report_Fix_Abstract {

		public function fix() {
			return exec($this->getCommand());
		}
		
		public function asText() {
			return $this->getCommand();
		}
		
	}