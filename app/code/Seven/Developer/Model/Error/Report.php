<?php 

	class Developer_Model_Error_Report extends Core_Model_Entity {
		
		protected $_alias = 'developer/error_report';
		
		public function loadData($data, $raw = false) {
			parent::loadData($data, $raw);
			if(file_exists($this->getFile())) {
				$this->setSize(filesize($this->getFile()));
				$start_tag = preg_quote("<!-- exclude_hash_begin -->");
				$end_tag = preg_quote("<!-- exclude_hash_end -->");
				$content = file_get_contents($this->getFile());
				$content = preg_replace('/[\t ]+/', '', preg_replace("/{$start_tag}.*{$end_tag}/imsU", "", $content));
				$this->setMd5(md5($content));
				$this->setName(preg_replace('/\.html$/', '', basename($this->getFile())));
				$this->setDate(date('Y-m-d H:i:s', filemtime($this->getFile())));
			}
			return $this;
		}
		
	}