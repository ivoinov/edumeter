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

    abstract class Core_Model_Form extends Core_Model_Abstract {
        
        protected  $_fields = array();

        public function removeField($id) {
            unset($this->_fields[$id]);
            return $this;
        }
        
        public function addField($id, $field) {
        	if(is_array($field))
        		$field = $this->_getFieldInstance($field);
        	else if($field instanceof Core_Model_Entity_Attribute_Abstract)
        		$field = $field->getInputModel();
        		
            $field->setForm($this);
            
            if(!$field->getName())
                $field->setName($id);
                
            if($field->hasDefault())
            	$field->setValue($field->getDefault());
            
            $this->_fields[$id] = $field;
            return $this;
        }

        /**
         * @param array $data
         * @return Core_Model_Form_Input_Abstract
         */
        
        protected function _getFieldInstance($data) {
            if(isset ($data['model'])) {
                $model = $data['model'];
            } else {
            	if(!isset($data['type']))
            		$data['type'] = 'core/text';
            	if(strpos($data['type'], '/') === false) 
            		$data['type'] = 'core/' . $data['type'];
            		 
				list($prefix, $type) = explode('/', $data['type'], 2);
            	$model = $prefix . "/form_input_" . $type;
            }
        	return Seven::getModel($model, $data);
        }
        
        /**
         * @return array
         */
        
        public function getValues() {
            $values = array();
            $root = &$values;
            foreach($this->getFields() as $id => $field) {
                if(!$field->getOptional() || $field->isSubmit()) {
                    foreach((array)$field->getNameArray() as $key) {
                        if(!isset($values[$key]))
                            $values[$key] = array();
                        $values = &$values[$key];
                    }
                    $values = $field->getValue();
                }
                $values = &$root;
            }
            return $root;
        }
        
        public function getValue($id) {
            if($field = $this->getField($id))
                if(!$field->getOptional() || $field->isSubmit())
                    return $field->getValue();
            return null;
        }
        
        public function getField($id){
            if(isset($this->_fields[$id]))
                return $this->_fields[$id];
            return NULL;
        }

        public function getFields() {
            return (array)$this->_fields;
        }
        
        final public function save(){            
            foreach ($this->getFields() as $name => $field) {
                $field->save();
            }
            return $this->_save();
        }
        
        protected function _save(){
            
        }
                
        protected function _load($source = array()){
            
        }
                
        public function load($source = array()){
            // initialize source
            if(!($source instanceof Seven_Object))
                $source = new Seven_Object($source);
            
            // load data from source
            foreach ($this->getFields() as $field) {
                $name = $field->getId();

             	if($source->hasData($name))
                    $field->setValue($source->getData($name));
            }
            
            // additional actions for loaded data
            $this->_load($source);
            
            // load data from request
            foreach ($this->getFields() as $name => $field)
                $field->load();
            
            return $this;
        }
                
        public function isValid(){   
            $valid = true;
            foreach ($this->getFields() as $field)
                 $valid = $field->isValid() && $valid;
            return $valid;
        } 
                        
        public function isSubmit(){
            foreach ($this->getFields() as $field)
                if(!$field->isSubmit())
                    return false;
             return true;
        }

    }