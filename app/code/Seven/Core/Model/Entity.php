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

    class Core_Model_Entity extends Core_Model_Abstract {

        protected $_search_field = NULL;
        protected $_search_value = NULL;
        protected $_alias = NULL;
        
    	public function __construct($data = array()) {
			$this->_getResource()->init($this);
    		parent::__construct($data);
       	}
    	
        public function load($id, $id_field = NULL) {
            $this->_getResource()->load($this, $id, $id_field);
            return $this;
        }

        public function save() {
            $this->_getResource()->save($this);
            return $this;
        }
        
        public function remove() {
            $this->_getResource()->remove($this);
            $this->setData($this->_getKey(), 0);
            return $this;
        }
        
        /**
         * @return Core_Resource_Entity_Collection
         */
        
        public function getCollection() {
            return $this->_getResource()->getCollection();
        }

        public function _getId() {
            return $this->getData($this->_getKey(), true);
        }
        
        public function _setId($id = false) {
            if($id === false)
                $id = $this->getData($this->_getKey(), true);
            else
                $this->setData($this->_getKey(), $id, true);
            $this->_setSearchField($this->_getKey());
            $this->_setSearchValue($id);
            return $this;
        }
        
        public function _isNew() {
        	return $this->isNew();
        }
        
        public function isNew() {
            return !$this->_getSearchField() && ($this->_getSearchValue() === null);
        }
        
        public function isLoaded() {
        	return !$this->isNew();
        }
        
        public function _getAlias() {
            if($this->_alias === NULL)
                    $this->_alias = Seven::getAliasByClass(get_class($this), "model");
            return $this->_alias;
        }
        
        public function _getKey() {
        	return $this->_getResource()->getKey();
        }
        
        public function _setNew() {
        	$this->_setSearchField(NULL);
        	$this->_setSearchValue(NULL);
        	return $this;
        }
        
        public function _setSearchField($field) {
            $this->_search_field = $field;
            return $this;
        }

        public function _setSearchValue($value) {
            $this->_search_value = $value;
            return $this;
        }

        public function _getSearchField() {
            return $this->_search_field;
        }

        public function _getSearchValue() {
            return $this->_search_value;
        }
        
        /**
         * @return Core_Resource_Entity
         */
        
        public function _getResource() {
            return Seven::getResource($this->_getAlias());
        }
     
        /**
         * @param array $data
         * @param boolean $raw
         */
        
        public function loadData($data, $raw = true) {
            $this->_setOriginalData($data);
            if($raw) {
	            $data = $this->_processRawData($data);
            }
            $this->addData($data);
            $this->_setId();
            return $this;
        }
        
        /**
         * @param array $data
         * @return array
         */
        
        protected function _processRawData($data) {
        	foreach($data as $key => $value) {
        		$attribute = $this->getAttribute($key);
        		if($attribute->getSerializable()) {
        			try {
        				$data[$key] = unserialize($value);
        			} catch(Core_Exception_Notice $e) {
        	
        			}
        		}
        	}
        	return $data;        	
        }
        
        /**
         * @var array
         */
        
        protected $_original_data = null;
        
        /**
         * @param array $data
         */
        
        protected function _setOriginalData($data) {
            $this->_original_data = $data;
            return $this;
        }
        
        /**
         * @return array
         */
        
        public function _getOriginalData() {
            return $this->_original_data;
        }

        /**
         * Check read access to this entity
         * This method used in standard CRUD controller
         *
         * @return boolean
         */
        
        public function canRead() {
        	return true;
        }

        /**
         * Check update access to this entity
         * This method used in standard CRUD controller
         *
         * @return boolean
         */
        
        public function canUpdate() {
        	return $this->canRead();
        }

        /**
         * Check create access for entities of this type
         * This method used in standard CRUD controller
         *
         * @return boolean
         */
        
        public function canCreate() {
        	return true;
        }

        /**
         * Check delete access to this entity
         * This method used in standard CRUD controller
         *
         * @return boolean
         */
        
        public function canDelete() {
        	return $this->canUpdate();
        }
        
        /**
         * @deprecated since 1.0.0 use getAttributes instead
         */
        
        public function _getAttributes() {
        	return $this->getAttributes();
        }
        
        /**
         * @return array
         */
        
        public function getAttributes() {
        	return $this->_getResource()->getAttributes();
        }
    
        /**
         * Return an attribute instance
         * 
         * @param string $key
         * @return Core_Model_Entity_Attribute_Abstract
         */
        
        public function getAttribute($key) {
        	return $this->_getResource()->getAttribute($key);
        } 
        
        /**
         * Return an formatted, special chars escaped value of attribute
         * (basic implementation)
         * @todo  Implement this method
         * @param string $attribute_name
         * @param array $options
         */
        
        public function formatData($attribute_name, $options = array()) {
        	return $this->getAttribute($attribute_name)
        			->formatValue($this->getData($attribute_name, true), $options);
        }
        
        /**
         * @see lib/Seven/Seven_Object::__call()
         */
        
        public function __call($method_name, $args) {
        	if(substr($method_name, 0, 6) === 'format') {
        		return call_user_func_array(array($this, 'formatData'), array_merge(array($this->_getDataKeyByMethod($method_name, 6)), $args));
        	}
        	return parent::__call($method_name, $args);
        }
        
    }