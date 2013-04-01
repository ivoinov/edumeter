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

    abstract class Core_Model_Form_Input_Abstract extends Core_Model_Abstract {

        protected $_validators = array();

        protected $_filters	   = array();
        
        public function __construct($data = array()) {
        	parent::__construct($data);
        	if(!isset($this->_data['validators']))
        		$this->_data['validators'] = array();
        	if(!isset($this->_data['filters']))
        		$this->_data['filters'] = array();
        	$this->_filters = &$this->_data['filters'];
        	$this->_validators = &$this->_data['validators'];
        }
        
        public function getBlockAlias() {
        	if($this->hasBlock())
        		return $this->getBlock();
        	if($this->getType()) {
        		if(strpos($this->getType(), '/') === false) $this->setType('core/' . $this->getType());
        		list($prefix, $type) = explode('/', $this->getType(), 2);
        		return $prefix . '/widget_form_input_' . $type;
        	}
        	return 'core/widget_form_input_text';
        }
        
        public function isValid() {
        	if($this->getUseDefault() === true)
        		return true;
            if(!$this->getRequired() && !$this->getValue()) // skip all checks if value is empty and field not required
                return true;
            foreach($this->getValidators() as $name => $validator_callback) {
            	if($validator_callback == 'skip' || empty($validator_callback)) continue;
                if(!call_seven_callback_array($validator_callback, array($this->getValue(), $this), 'helper')) {
                	$messages = $this->getValidationMessages();
                	if(isset($messages[$name]))
                		$this->setErrorMessage($messages[$name]);
                	else if(!$this->getErrorMessage() && isset($messages['default'])) 
                		$this->setErrorMessage($messages['default']);
                    return false;
                }
            }
            return true;
        }
        
        public function applyFilters($value) {
        	foreach($this->getSortedFilters() as $filter) {
        		$value = call_seven_callback_array($filter, array($value, $this), 'helper');
        	}
        	return $value;
        }
        
        public function addValidator($id, $callback){
            $this->_validators[$id] = $callback;
            return $this;
        }

        public function removeValidator($id){
            unset ($this->_validators[$id]);
            return $this;
        }

        public function getValidators(){
            return $this->_validators;
        }
        
        public function addFilter($id, $callback){
            $this->_filters[$id] = $callback;
            return $this;
        }

        public function removeFilter($id){
            unset($this->_filters[$id]);
            return $this;
        }

        public function getFilters(){
            return $this->_filters;
        }
        
        protected function _loadIsDefault() {
        	if($this->getUseDefault() !== NULL && $this->isSubmit())
        		$this->setUseDefault(Seven::app()->getRequest()->getPost('defaults.' . $this->getId('.')) ? true : false);
        } 
        
        public function load(){
        	$this->_loadIsDefault();
            if(!$this->isSubmit())
                return;
            $this->setValue(($this->getUseDefault() === true) ? NULL : $this->applyFilters($this->_getRequestValue()));
        }
                
        public function isSubmit() {
            return $this->_getRequestValue() !== null;            
        }
        
        protected function _getRequestValue() {
        	$source = $this->getSource();
            foreach($this->getNameArray() as $key) {
                if(isset($source[$key]))
                    $source = $source[$key];
                else
                    return null;
            }
            return $source;
        }
        
        public function getHtmlName() {
        	if($this->hasHtmlName())
        		return parent::getHtmlName();
        	return $this->getName();
        }
        
        public function getId($glue = '.') {
            return implode($glue, $this->getNameArray());
        }

        protected function _getSource() {
            return $this->getSource();
        }
        
        public function getSource() {
        	if($this->getForm() && strtoupper($this->getForm()->getMethod()) == "GET")
                return Seven::app()->getRequest()->getParameters();
            return Seven::app()->getRequest()->getPost();
        }
        
        public function save(){
            
        }
        
        public function getNameArray() {
            if(strpos($this->getHtmlName(), '[') === false)
                return array($this->getHtmlName());
            return array_map(function($item) { return trim($item, ']'); }, explode('[', $this->getHtmlName()));
        }

        public function getSortedFilters() {
        	return $this->_filters;	
        }

    }
