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

    class Core_Model_Form_Input_Grid extends Core_Model_Form_Input_Abstract {
    	
    	protected $_fields = array();
    	
    	public function getFields() {
    		if(!$this->_fields) {
    			foreach((array) parent::getFields() as $name => $field) {
    				$field['parent'] = $this;
    				$type = isset($field['type']) ? $field['type'] : 'text';
    				$classname = Seven::getClassByAlias('core/widget_grid_column_' . $type, 'block'); // TODO: Are you sure that type resolved OK?
    				if(is_subclass_of($classname, 'Core_Block_Widget_Grid_Column_Input_Abstract')) {
						if(!isset($field['html_name']))
    						$field['html_name'] = $this->getHtmlName() . '[' . $name . ']';
    					$this->_fields[$name] = Seven::getModel('core/form_input_grid_' . $type, $field);
    				}
    			}
    		}
    		return $this->_fields;
    	}
    	
    	public function getField($id) {
    		$fields = $this->getFields();
    		if(isset($fields[$id]))
    			return $fields[$id];
    		return null;
    	}
		
		public function setValue($value) {
			foreach($this->getFields() as $name => $field) {
				if(!isset($value[$name])) continue;
				$field->setValue($value[$name]);
			}
			return $this;
		}
		
		public function getValue() {
			$values = array();
			foreach($this->getFields() as $name => $field) {
				$values[$name] = $field->getValue();
			}
			return $values;
		}
    	
		public function load() {
			foreach($this->getFields() as $field) 
				$field->load();
			return $this;
		}
    	
    }
