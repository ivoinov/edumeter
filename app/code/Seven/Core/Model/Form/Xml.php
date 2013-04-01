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

    class Core_Model_Form_Xml extends Core_Model_Form {
                         
    	public function _getFormXmlData($alias) {
            if(strpos($alias, "/") === false) $alias = "core/" . $alias;
            list($module, $form_name) = explode("/", $alias, 2);                        
    		return Seven::getConfig('forms/'.$module.'/'.$form_name);
    	}
    	
    	protected function _extendForm($data) {
			if(is_array($data)) {
				if(isset($data['extends']) && is_array($data['extends'])) {
					foreach($data['extends'] as $formalias) { 
						if(!empty($formalias)) {
							$formdata = $this->_getFormXmlData($formalias);
							if(is_array($formdata)) {
								$this->_extendForm($formdata);
							}
						}
					}
				}
				$this->setData(array_merge_recursive_replace($this->getData(), $data));
			}
    		return $this;
    	}
    	
        public function initXml($alias){
        	$data = $this->_getFormXmlData($alias);
            if($data === NULL)
                throw new Exception ("XML Form node '".$alias."' not exists");

            $this->_extendForm($data);

            if(is_array($this->getData('fields'))) {
                foreach($this->getData('fields') as $key => $data) {
                	if(!empty($data['ignore'])) {
                		$this->removeField($key);
						continue;                		
                	}
                    $this->addField($key, $this->_extendFieldData($key, $data));
                }
            }
            return $this;
        }

        protected function _extendFieldData($key, $data) {
            if(!isset($data['source']) && $this->hasSource())
                $data['source'] = $this->getSource() . "/" . $key;

            $source_parts = isset($data['source']) ? explode("/", $data['source'], 3) : array();

            if(count($source_parts) == 3) {
                list($module, $entity, $field) = $source_parts;
                $attributes = Seven::getResource($module . '/' . $entity)->getAttributes();
                if(isset($attributes[$field])) {
                    return $attributes[$field]->getInputModel($data);
                }
            }
            return $data;
        }
        
    }


