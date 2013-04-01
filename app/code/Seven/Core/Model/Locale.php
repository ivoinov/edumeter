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
    
    class Core_Model_Locale extends Core_Model_Entity {
        
    	protected $_alias = "core/locale";
    	
        public function getOptionsArray() {
            $options = array();
            foreach($this->getCollection()->load() as $item) {
                $options[$item->_getId()] = $item->getName();
            }
            return $options;
        }
        
        public function translate($text) {
            $dictionary = $this->getDictionary();
            $args = func_get_args();
            if(isset($dictionary[$text])) {
                $text = $dictionary[$text];
                if(is_array($text)) { // it's a plural translate
                    $arg = 1;
                    foreach($args as $arg)
                        if(!is_string($arg) && is_numeric($arg))
                            break;
                    $form = $this->_getPluralForm($arg);
                    $text = isset($text[$form]) ? $text[$form] : reset($text);
                }
            }
            $args[0] = preg_replace('/^[a-zA-Z_]+::/', '', $text);
            return call_user_func_array('sprintf', $args);
        }
                
        protected $_dictionary = null;
        
        public function getDictionary() {
            if($this->_dictionary === null) {
            	$this->_dictionary = array();
                $this->_getResource()->loadDictionary($this);
            }
            return $this->_dictionary;
        }
        
        public function setDictionary($dict) {
        	$this->_dictionary = $dict;
        	return $this;
        }
        
        public function addDictionaryPath($path, $load_immidiately = true) {
        	$paths = $this->getDictionaryPaths();
        	$paths[$path] = $path;
        	$this->setDictionaryPaths($paths);
        	if($load_immidiately) {
        		$this->getDictionary();
        		$this->_getResource()->loadDictionary($this);
        	}
        	return $this;
        }
        
        public function refresh() {
        	$this->_dictionary = null;
        }
        
        public function getDictionaryPaths() {
        	$paths = parent::getDictionaryPaths();
        	if($paths === NULL) {
        		$paths = array(BP . DS . "app" . DS . "i18n");
				foreach(Seven::app()->getLoadedPackages() as $package) {
					$paths[] = $package->getBasePath() . DS . "i18n";
				}	
        		$this->setDictionaryPaths($paths);
        	}
        	return $paths;
        }
        
        public function getPluralFormula() {
        	return isset($this->_dictionary[':plural:']) ? $this->_dictionary[':plural:'] : null;
        }
        
        public function _getPluralForm($n) {
            $this->getDictionary(); // load dictionary
            if($this->getPluralFormula()) {
                try {
                    // TODO: Maybe there exists better way to know plural form?
                	$form = 0;
                    @eval("\$form = (int) (" . str_replace('n', $n, $this->getPluralFormula()) . ");");
                    return $form;
                } catch(Exception $e) { }
            }
            return 0;
        }
    
        public function addLoadedDictionaryPath($path) {
        	$paths = (array)$this->getLoadedDictionaryPaths();
        	$paths[$path] = true;
        	return $this->setLoadedDictionaryPaths($paths);
        }
        
        public function isLoadedDictionaryPath($path) {
        	$paths = (array)$this->getLoadedDictionaryPaths();
        	return isset($paths[$path]);
        }
        
        public function load($id, $id_field = NULL) {
        	$object = parent::load($id, $id_field);
        	if($dct = $object->getDictionaryFile())
        		$object->addDictionaryPath(BP . DS . seven_file($dct)->getPath());
        	return $object;
        }
        
        public function getLanguage() {
        	return parent::getLanguage() ?: $this->_getId();
        }
        
    }
        