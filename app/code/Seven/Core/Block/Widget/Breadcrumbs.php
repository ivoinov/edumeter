<?php 

	class Core_Block_Widget_Breadcrumbs extends Core_Block_Template {
		
		public function __construct($data = array()) {
			if(isset($data['template']))
				$data['template'] = 'page/breadcrumbs.phtml';
			parent::__construct($data);
		}
		
		public function getModel() {
			return Seven::getSingleton('core/breadcrumbs');
		}
	
		public function getCrumbs() {
			return $this->getModel()->getCrumbs();
		}
		
		public function addCrumb($id, $data) {
			return $this->getModel()->addCrumb($id, $data);
		}
		
		public function setCrumb($id, $key, $value = null) {
			return $this->getModel()->setCrumb($id, $key, $value);
		}
		
		public function removeCrumb($id) {
			return $this->getModel()->removeCrumb($id);
		}
		
	}