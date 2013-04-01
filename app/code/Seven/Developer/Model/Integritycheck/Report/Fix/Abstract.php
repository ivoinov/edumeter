<?php

	abstract class Developer_Model_Integritycheck_Report_Fix_Abstract extends Core_Model_Abstract {
		
		abstract public function asText();
		
		public function __toString() {
			return $this->asText();
		}
		
		abstract public function fix();
		
	}