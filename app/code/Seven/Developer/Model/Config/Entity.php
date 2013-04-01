<?php

    class Developer_Model_Config_Entity extends Core_Model_Entity {
    	
    	public function getEntityAttributes() {
    		$attributes = array();
    		if($resource = $this->getEntityResource()) {
	    		$attributes = $resource->getAttributes() ?: array();
	    		if($this->isMultiview()) {
					$attr_type = 'number';
					foreach($this->getEntityModel()->_getViews() as $value => $view) 
						if(!is_numeric($value))
							$attr_type = 'text';
	    			$attributes['_view_id'] = Seven::getModel('core/entity_attribute_' . $attr_type, array('multiview' => true, 'nullable' => false));
	    		}
    		}
    		return $attributes;
    	}

    	public function getEntityModel() {
    		try {
    			if(class_exists(Seven::getClassByAlias($this->_getId(), 'model')))
    				return Seven::getModel($this->_getId());
    		} catch(Exception $e) {
    		}
    		return null; 
    	}
    	
    	public function getEntityResource() {
    	    try {
    	    	if(class_exists(Seven::getClassByAlias($this->_getId(), 'resource')))
    				return Seven::getResource($this->_getId());
    		} catch(Exception $e) {
    		}
    		return null; 
    	}

    	public function getEntityTable() {
    		if($resource = $this->getEntityResource())
	    		return $resource->getTable();
    	}
    	
    	public function getEntityTables() {
    		$tables = array($this->getEntityTable());
    		if($this->isMultiview() && $this->getViewsTable())
    			$tables[] = $this->getViewsTable();
    		return array_filter(array_unique($tables));
    	}
    	
    	public function getViewsTable() {
    		if(!$this->isMultiview())
    			return null;
    		$resource = $this->getEntityResource();
    		return $resource->getViewsTable();
    	}
    	
    	public function getPrimaryKey() {
    		if($this->getEntityResource() instanceof Core_Resource_Entity)
    			return $this->getEntityResource()->getKey();
    		return null;
    	}
    	
    	public function getEntityTableFields($table) {
   			return $this->_getResource()->getEntityTableFields($this, $table);
    	}
    	
    	public function isMultiview() {
    		return $this->getEntityResource() instanceof Core_Resource_Multiview_Entity;
    	}
    	
    }