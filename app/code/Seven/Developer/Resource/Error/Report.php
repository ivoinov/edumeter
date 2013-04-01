<?php 

	class Developer_Resource_Error_Report extends Core_Resource_Entity {
		
		protected $_alias = 'developer/error_report';

		public function init(&$object) {
			return $object;
		}

		public function load(&$object, $id, $id_field = NULL) {
			$report_filename = BP . DS . 'var' . DS . 'reports' . DS . $id . '.html';
			if(file_exists($report_filename)) {
				$object->loadData(array('file' => $report_filename))->_setId($id);
				$object->_setSearchField($id_field);
				$object->_setSearchValue($id);
				$this->_afterLoad($object);
			}
		
			return $object;
		}
		
	}