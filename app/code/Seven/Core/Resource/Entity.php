<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   Seven
 * @package    Core
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Core_Resource_Entity extends Core_Resource_Db {
	
	protected $_alias;
	protected $_key;
	protected $_table;
	protected $_attributes = null;
	protected $_defaults = null;	
	
	public function init(&$object) {
		$object->setData($this->_getInitData());
		return $object;
	}
	
	protected function _getInitData() {
		if($this->_defaults === null) {
			$this->_defaults = array();
			foreach($this->getAttributes() as $key => $attribute) {
				if($attribute->getDefault() !== null)
					$this->_defaults[$key] = $attribute->getDefault();
			}
		}
		return $this->_defaults;
	}
	
	public function load(&$object, $id, $id_field = NULL) {
		if(empty($id_field))
			$id_field = $this->getKey();
		
		if(Seven::getConfig('resources/identity_map') && $id && $id_field == $this->getKey()) {
			$prim_id = $this->_getIdentityId($object, $id);
			$identity_map = Seven::getObjectCache($this->getAlias() . '::identity_map');
			if(array_key_exists($prim_id, (array) $identity_map)) {
				$object->addData($identity_map[$prim_id]->getData());
				$object->_setSearchField($id_field);
				$object->_setSearchValue($id);
				return $object = $identity_map[$prim_id];
			}
			$identity_map[$prim_id] = $object;
		}
		
		$data = $this->_getLoadQuery($object, $id, $id_field)->load();
		
		if(count($data)) {
			$object->loadData(reset($data));
			$object->_setSearchField($id_field);
			$object->_setSearchValue($id);
			$this->_afterLoad($object);
		}
		
		return $object;
	}
	
	protected function _afterLoad(&$object) {
	}
	
	/**
	 * Return special ID for identity map
	 * @param Core_Model_Entity $object 
	 * @param mixed $id loaded by
	 */
	
	public function _getIdentityId($object, $id) {
		return $id;
	}
	
	/**
	 * Query for loading one item
	 * 
	 * @param Seven_Object $object
	 * @param string $id
	 * @param string $id_field
	 */
	
	protected function _getLoadQuery($object, $id, $id_field) {
		return $this->getCollection()->filter($id_field, $id)->getSelect();
	}
	
	/**
	 * Query for inserting new item
	 * 
	 * @param Seven_Object $object
	 * @param array $values
	 * @param string $table
	 */
	
	protected function _getInsertQuery($object, $values) {
		return $this->getConnection()->insert($this->getTable(), $values);
	}
	
	/**
	 * Query for updating exists item
	 * 
	 * @param Seven_Object $object
	 * @param array $values
	 * @param string $table
	 */
	
	protected function _getUpdateQuery($object, $values) {
		return $this->getConnection()->update($this->getTable(), $values, array($object->_getSearchField() => $object->_getSearchValue()));
	}
	
	public function save(&$object) {
		$values = $this->_getFieldsUpdate($object, $this->getTable());
		if(($object->_getSearchField() === NULL) || ($object->_getSearchValue() === NULL)) {
			$this->_beforeInsert($object);
			$insert = $this->_getInsertQuery($object, $values);
			if($last_id = $this->getConnection()->lastInsertId())
				$object->_setId($last_id);
			$this->_afterInsert($object);
		} else {
			$this->_beforeUpdate($object);
			if(count($values))
				$this->_getUpdateQuery($object, $values, array($object->_getSearchField() => $object->_getSearchValue()));
			$this->_afterUpdate($object);
		}
		$object->loadData($object->getData(), false);
	}
	
	protected function _beforeInsert($object) {
	}
	
	protected function _beforeUpdate($object) {
	}
	
	protected function _afterInsert($object) {
	}
	
	protected function _afterUpdate($object) {
	}
	
	protected function _getFieldsUpdate($object, $table) {
		$columns = $this->getConnection()->describeTable($table);
		$original_data = $object->_getOriginalData();
		$values = array();
		foreach($columns as $key => $description) {
			$value = $object->getData($key);
			if($attribute = $this->getAttribute($key)) {
				if($attribute->getSerializable())
					$value = serialize($value);
			}
			if(array_key_exists($key, (array) $original_data)) // skip values which are the same
				if($original_data[$key] === $value)
					continue;
			$values[$key] = $value;
		
		}
		return $values;
	}
	
    public function getAttribute($name) {
       	$attributes = $this->getAttributes();
       	if(isset($attributes[$name]))
       		return $attributes[$name];
       	return Seven::getModel('core/entity_attribute_text');
    }
        
	
	/**
	 * @deprecated use attributes instead
	 */
	
	public function getFieldOptions($key) {
		return new Seven_Object(array_merge((array)$this->getConfig('fields/' . $key), (array)$this->getConfig('attributes/' . $key)));
	}
	
	public function remove(&$object) {
		$table = $this->getTable();
		$adapter = $this->getConnection();
		if($object->isLoaded())
			$adapter->delete($table, array($object->_getSearchField() => $object->_getSearchValue()));
		return $this;
	}
	
	public function getCollection() {
		$collection = Seven::getCollection($this->getAlias());
		return $collection;
	}
	
	public function getAlias() {
		if($this->_alias === NULL)
			$this->_alias = Seven::getAliasByClass(get_class($this), "resource");
		return $this->_alias;
	}
	
	public function getTable() {
		if($this->_table === NULL) {
			$this->_table = $this->getConfig('table');
			if(empty($this->_table)) {
				throw new Exception("Table for " . get_class($this) . " (" . $this->getAlias() . ") not specified in XML");
			}
		}
		return $this->_table;
	}
	
	public function getKey() {
		if($this->_key === NULL) {
			$this->_key = Seven::getConfig("entity/" . $this->getAlias() . "/key");
			if(empty($this->_key))
				throw new Exception("Key for " . get_class($this) . " not specified in XML");
		}
		return $this->_key;
	}
	
	public function getTableNameByAlias($alias) {
		$table_name = Seven::getConfig("entity/" . $alias . "/table");
		if(empty($table_name))
			throw new Exception("Table for alias" . $alias . " not specified in XML");
		return $table_name;
	}
	
	public function getConfig($path) {
		return Seven::getConfig("entity/" . $this->getAlias() . "/" . trim($path, '/'));		
	}
	
	public function getAttributes() {
		if($this->_attributes === null) {
			$this->_attributes = $this->getConfig('attributes');
			// convert array to objects
			if(is_array($this->_attributes)) {
				array_walk($this->_attributes, function(&$data, $key) { 
					$data['key'] = $key; 
					if(empty($data['type'])) $data['type'] = 'text';
					$data = Seven::getSingleton('core/entity_attribute')
								->getTypeInstance($data['type'], (array)$data); 
				});
			} else {
				$this->_attributes = array();
			}
		}
		return $this->_attributes;
	}
	
	public function isUniqueValue($attribute, $value) {
		return !$this->getConnection()->select($this->getTable(), 'count(*)')
					->filter($attribute, $value)
					->fetchOne();
	}
    
}