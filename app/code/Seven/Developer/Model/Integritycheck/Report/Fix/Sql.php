<?php

	class Developer_Model_Integritycheck_Report_Fix_Sql extends Developer_Model_Integritycheck_Report_Fix_Abstract {
		
		public function asText() {
			return $this->getQuery();
		}
		
		public function fix() {
			return $this->getConnection()->query($this->getQuery());
		}
		
	}