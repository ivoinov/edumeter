<?php

	class Developer_Helper_Errorreporting_Output {
	
		public function getReportUrl($value, $report) {
			return sprintf("<a href='%s'>%s</a>", seven_url('*/*/view', array('id' => $report->_getId())), $value);
		}
		
	}