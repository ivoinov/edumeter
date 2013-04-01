<?php

	class Seven_Developer_Controller_Routing extends Core_Controller_Abstract {
	
		public function indexAction() {
			Seven::register('routing_map', Seven::getConfig('routing/routes', $this->getRequest()->getParam('area')));
			$this->getLayout()->addTag('developer_routes_page');
			$this->render();
		}
	
	}