<?php

    class Developer_Resource_Config_Entity_Collection extends Core_Resource_Entity_Collection {

    	public function getSelect() {
    		return null;
    	}
    	
    	public function where($key, $value = NULL){;
    	$this->_loaded = false;
    	return $this;
    	}
    	
    	public function filter($key, $condition) {
    		$this->_loaded = false;
    		return $this;
    	}
    	
    	public function orFilter($key, $condition) {
    		$this->_loaded = false;
    		return $this;
    	}
    	
    	public function order($order) {
    		$this->_loaded = false;
    		$args = func_get_args();
    		call_user_func_array(array($this->getSelect(), 'order'), $args);
    		return $this;
    	}
    	
    	public function load($force = false) {
    		if($this->_loaded && !$force)
    			return $this;
    		$this->_loaded = true;
    		$collection = array();
    		foreach(Seven::getConfig('entity') as $group_id => $group) {
    			foreach($group as $entity_name => $entity_data) {
    				$entity_data[$this->getKey()] = $group_id . '/' . $entity_name;
    				//$entity_data['group'] = $group_id;
    				//$entity_data['name'] = $entity_name;
	    			$object = $this->_createModel($entity_data);
	    			$collection[] = $object;
    			}
    		}
    		$this->exchangeArray($collection);
    		return $this;
    	}
    	
    	public function limit($limit, $offset = 0) {
    		//$this->getSelect()->limit($limit, $offset);
    		return $this;
    	}

    	public function countAll() {
    		return count(Seven::getConfig('entity', 'global'));
    	}
    }
    