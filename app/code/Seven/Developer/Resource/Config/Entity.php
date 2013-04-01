<?php

    class Developer_Resource_Config_Entity extends Core_Resource_Entity {

    	public function init(&$object) {
    		$object->setData(array());
    		return $object;
    	}
    	
    	public function load(&$object, $id, $key = null) {
    		$object->setData(Seven::getConfig('entity/' . $id, 'global'));
    		$object->_setId($id);
    	} 

    	public function getAttributes() {
    		return array();
    	}

    	public function getKey() {
    		return 'entity_id';
    	}
    	
    	public function getEntityTableFields($object, $table) {
    		try {
    			if(!$object->getEntityResource() || !($object->getEntityResource() instanceof Core_Resource_Db))
    				throw new Exception('Can\'t obtain database connection');
    			$connection = $object->getEntityResource()->getConnection();
    			$description = $connection->describeTable($table);
    			return $description;
    		} catch(Exception $e) {
    			
    		}
    		return array();
    	}
    	
    }