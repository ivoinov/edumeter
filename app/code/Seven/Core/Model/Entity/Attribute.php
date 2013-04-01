<?php 

	class Core_Model_Entity_Attribute {
		
		public function getTypeInstance($type, $data = array()) {
			$model = Seven::getConfig('attribute_types/' . str_replace('/', '_', $type) . '/model');
			return Seven::getModel($model ?: 'core/entity_attribute_text', $data);			
		}
		
	}
	
	