<?php 

	class Developer_Resource_Error_Report_Collection extends Core_Resource_Entity_Collection {

		protected $_alias = 'developer/error_report';
		
		protected $_limit = null;
		protected $_offset = 0;

		public function getSelect() {
			return null;
		}
	    
	    public function countAll() {
			return $this->count();
	    }
		
		public function load($force = false) {
			if($this->_loaded && !$force)
				return $this;
			$this->_loaded = true;
			
			$collection = array();
			$offset = $this->_offset;
			
			foreach(glob(BP . DS . 'var' . DS . 'reports' . DS . '*.html') as $report_filename) {
				if($offset-- > 0)
					continue;
				if($this->_limit !== null && $this->_limit <= count($collection)) 
					break;
				$collection[] = $this->_createModel(array(
					'id' 	=> preg_replace('/\.html$/', '', basename($report_filename)), 
					'file' 	=> $report_filename, 
				));
			}
			
			$this->exchangeArray($collection);
			return $this;
		}
		
	    public function limit($limit, $offset = 0) {
	    	$this->_limit = $limit;
	    	$this->_offset = $offset;
	    	$this->_loaded = false;
	    	return $this;
	    } 
		
	}