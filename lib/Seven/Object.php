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
 * @package    Libs
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

    class Seven_Object implements ArrayAccess {

        protected $_data = array();

        public function __construct($data = array()) {
            if(!empty($data))
                $this->setData($data);
        }

        public function setData($key, $value = NULL, $flat_key = false) {
            if(is_array($key) && $value === NULL) {
                $this->_data = $key;
            } else if(!$flat_key && strpos($key, '.') !== false) {
                $data = &$this->_data;
                foreach(explode('.', $key) as $part) {
                    if(!isset($data[$part])) {
                        $data[$part] = array();
                    }
                    $data = &$data[$part];
                }
                $data = $value;
            } else {
                $this->_data[$key] = $value;
            }
            return $this;
        }

        public function getData($key = NULL, $flat_key = false) {
            if($key === NULL)
                return $this->_data;

            if(!$flat_key && strpos($key, '.') !== false) {
                $data = $this->_data;
                foreach(explode('.', $key) as $part) {
                    if(!isset($data[$part]))
                        return NULL;
                    if(is_array($data[$part]))
                        $data = &$data[$part];
                    else
                        $data = $data[$part];
                }
                return $data;
            } else if(isset($this->_data[$key]))
                return $this->_data[$key];
            return NULL;
        }

        public function addData($key, $value = NULL, $flat_key = false) {
            if(is_array($key) && $value === NULL) {
                $this->_data = array_merge($this->_data, $key);
            } else {
                $this->setData($key, $value, $flat_key);
            }
            return $this;
        }

        public function unsData($key, $flat_key = false) {
            if(!$flat_key && strpos($key, '.') !== false) {
                $data = &$this->_data;
                $keys = explode('.', $key);
                $lastkey = end($keys);
                foreach(array_splice($keys, -1) as $part) {
                    if(!isset($data[$part]))
                        return $this;
                    if(is_array($data[$part]))
                        $data = &$data[$part];
                    else
                        $data = $data[$part];
                }
                unset($data[$lastkey]);
            } else { 
                unset($this->_data[$key]);
            }
            return $this;
        }

        public function hasData($key, $flat_key = false) {
            if(!$flat_key && strpos($key, '.') !== false) {
                $data = $this->_data;
                foreach(explode('.', $key) as $part) {
                    if(!isset($data[$part]))
                        return false;
                    if(is_array($data[$part]))
                        $data = &$data[$part];
                    else
                        $data = $data[$part];
                }
                return true;
            }             
            return isset($this->_data[$key]);
        }

        public function __call($method_name, $args) {
            $prefix = substr($method_name, 0, 3);
            switch($prefix) {
                case 'get':
                    return $this->getData($this->_getDataKeyByMethod($method_name), true);
                case 'set':
                    if(count($args) == 0) throw new Exception("Set value not specified");
                    return $this->setData($this->_getDataKeyByMethod($method_name), $args[0], true);
                case 'uns':
                    return $this->unsData($this->_getDataKeyByMethod($method_name), true);
                case 'has':
                    return $this->hasData($this->_getDataKeyByMethod($method_name), true);
            }
            throw new Exception("Method '".$method_name."' not exists");
        }

        protected function _getDataKeyByMethod($method_name, $prefix_length = 3) {
            return strtolower(preg_replace("/(.)([A-Z])/", "\\1_\\2", substr($method_name, $prefix_length)));
        }

        protected function _getMethodByDataKey($key_name) {
            return ucfirst(preg_replace_callback("/(.)_([a-z])/i", function($match) { return $match[1] . strtoupper($match[2]); }, $key_name));
        }

        public function offsetGet($offset) {
        	return $this->getData($offset, true);
        }
    
        public function offsetSet($offset, $value) {
        	return $this->setData($offset, $value, true);
        }
        
        public function offsetExists($offset) {
        	return $this->hasData($offset, true);
        }
     
        public function offsetUnset($offset) {
        	return $this->unsData($offset, true);
        }

        private $__set_real = false;
        protected $_virtual_property = array();

        /**
         * @param $prop
         * @return null
         */

        public function __get($prop) {
            $dynamicMethodName = 'get' . $this->_getMethodByDataKey($prop);
            $staticMethodName = '_' . $dynamicMethodName;

            if(method_exists($this, $dynamicMethodName))
                return $this->$dynamicMethodName();

            if(method_exists($this, $staticMethodName)) {
                $this->__set_real = true;
                $value = $this->$prop = $this->$staticMethodName();
                $this->__set_real = false;
                return $value;
            }

            if(isset($this->_data[$prop])) {
                $this->__set_real = true;
                $value = $this->$prop = $this->_data[$prop];
                $this->__set_real = false;
                return $value;
            }

            return null;
        }

        /**
         * @param $prop
         * @param $value
         * @return mixed
         */

        public function __set($prop, $value) {
            if($this->__set_real) {
                $this->_virtual_property[$prop] = true;
                return $this->$prop = $value;
            }

            $setterMethodName = 'set' . $this->_getMethodByDataKey($prop);
            if(method_exists($this, $setterMethodName))
                return $this->$setterMethodName($value);

            $this->_data[$prop] = $value;
        }

        /**
         * Reset all virtual properties
         */

        public function resetvp() {
            foreach($this->_virtual_property as $prop => $count)
                unset($this->$prop);
            $this->_virtual_property = array();
        }

    }