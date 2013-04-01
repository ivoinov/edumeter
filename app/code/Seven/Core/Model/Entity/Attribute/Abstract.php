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

	abstract class Core_Model_Entity_Attribute_Abstract extends Seven_Object {
		
		protected $_data_type   = 'varchar';
		
		public function getName() {
			return parent::getName() ?: $this->getLabel();
		}
		
		/**
		 * Return formated and special chars escaped attribute value
		 * @param mixed $value
		 * @param array $options
		 */
		
		public function formatValue($value, $options = array()) {
			if($this->getAllowHtml() && !$this->getAllowedTags())
				return $value;
			if($this->getAllowHtml() && $this->getAllowedTags())
				return strip_tags($value, "<" . implode('><', array_filter(array_map('trim', explode(',', (string)$this->getAllowedTags())))) . ">");
			return htmlspecialchars($value);
		}
		
		/**
		 * Return all possible values for this attribute or false if this attribute is any string
		 */
		
		public function getOptions() {
			return false;
		}

		/**
		 * MySQL data type
		 *
		 * @return string
		 */
		
		public function getDataType() {
			return parent::getDataType() ? : $this->_data_type;
		}
		
		/**
		 * Input widget block
		 * 
		 * @return Core_Block_Widget_Form_Input_Abstract
		 */
		
		public function getInputWidget($data = array()) {
                    $type = $this->_extractBlockType($data);
                    return Seven::getBlock($type ?: ($this->getBlock() ?: $this->_getBlock()), array_merge_recursive_replace($this->getData(), $data))
                                            ->setAttributeModel($this);
		}
		
		protected function _getBlock() {
			return 'core/widget_form_input_' . $this->getType();			
		}
		
		/**
		 * Input model
		 * 
		 * @return Core_Model_Form_Input_Abstract
		 */
		
		public function getInputModel($data = array()) {
                    $type = $this->_extractModelType($data);
                    return Seven::getModel($type ?: ($this->getModel() ?: $this->_getModel()), array_merge_recursive_replace($this->getData(), $data))
                                            ->setAttributeModel($this);
		}
		
		protected function _getModel() {
			return 'core/form_input_' . $this->getType();
		}
		
		public function getType() {
			return parent::getType() ?: 'text';
		}
                
                protected function _extractModelType($data) {
                    $model = false;
                    
                    if(isset ($data['model'])) {
                           $model = $data['model'];
                    } else if(isset($data['type'])) {
                           if(strpos($data['type'], '/') === false) 
                                $data['type'] = 'core/' . $data['type'];
                           list($prefix, $type) = explode('/', $data['type'], 2);
                           $model = $prefix . "/form_input_" . $type;
                    }                    
                    return $model;
                }
                
                protected function _extractBlockType($data) {
                    $model = false;
                    if(isset ($data['widget'])) {
                           $model = $data['widget'];
                    } else if(!isset($data['type'])) {
                           if(strpos($data['type'], '/') === false) 
                                   $data['type'] = 'core/' . $data['type'];
                           
                           list($prefix, $type) = explode('/', $data['type'], 2);
                           $model = $prefix . "/widget_form_input_" . $type;
                    }                    
                    return $model;
                }
		
	}