<?php 

	class Developer_Controller_Errorreporting extends Core_Controller_Crud_Abstract_List {
		
		public function indexAction() {
			$this->_listAbstract();
		}

		public function viewAction() {
			$entity = $this->_prepareView();
			Seven::app()->getResponse()->setBody(file_get_contents($entity->getFile()))->setIsAjax(true);
		}
		
	}