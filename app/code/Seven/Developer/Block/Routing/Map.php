<?php 

	class Seven_Developer_Block_Routing_Map extends Core_Block_Template {
		
		public function __construct($data = array()) {
			if(!isset($data['template']))
				$data['template'] = 'Seven/Developer/routing/map.phtml';
			parent::__construct($data);
		}
		
		public function getNodes() {
			return Seven::registered('routing_map') ? Seven::registry('routing_map') : array();
		}
		
	}