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

    class Core_Model_Request extends Core_Model_Url {
        
        /**
         * 
         * Initialize fields default value
         * 
         * @return Core_Model_Request 
         */

        public function init() {
            debug()->open("Init request");
            list($host) = explode(':', $_SERVER['HTTP_HOST'], 2);
            $this->setHost($host);
            $this->setScriptName(basename($_SERVER['SCRIPT_FILENAME']));
            list($path, $request) = explode($this->getScriptName(), trim($_SERVER['PHP_SELF'], "/"), 2);
            $this->setPort($_SERVER['SERVER_PORT']);
            $this->setRequest(trim($request, "/"));
            $this->setPath(trim($path, "/"));
            $this->setScheme((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');

            if(isset($_SERVER['QUERY_STRING'])) {
                $parameters = array();
                parse_str($_SERVER['QUERY_STRING'], $parameters);
                $this->setParameters($parameters);
            }
            debug()->close();
            debug()->log("Info", "Request", $this->getData());
            return $this;
        }
        
        /**
         * Convert $_FILES array to objects array
         */
        
        protected function _prepareFilesData(&$files, $property, $data) {
        	if(is_array($data)) {
	        	foreach($data as $key => $value) {
	        		if(!isset($files[$key]))
	        		    $files[$key] = array();
        			$this->_prepareFilesData($files[$key], $property, $value);
	        	}
        	} else {
	        	if(!is_object($files))
	        		$files = Seven::getModel('core/request_file', $files);
	        	$files->setData($property, $data);
        	}
        }
        
        /**
         * Get file option
         * 
         * @param string    $key     field key
         * @return string
         */
        
        public function getFile($key = NULL) {
        	$files = parent::getFiles();
            if(empty($files)) {
				foreach($_FILES as $name => $props) {
					foreach($props as $prop => $value) {
						$this->_prepareFilesData($files[$name], $prop, $value);
					}
				}
            }
        	if($key === NULL)
        		return $files;
            return isset($files[$key]) ? Seven::getModel('core/request_file', $files[$key]) : null;
        }
        
        /**
         *  POST Data
         */
        
        protected $_post = null;
        
        /**
         * Get post option
         * 
         * @param string $key
         * @return string
         */

        public function getPost($key = NULL, $default = NULL) {
            if($key === NULL) return $_POST;
            if($this->_post === null)
            	$this->_post = new Seven_Object($_POST);
            if($this->_post->hasData($key))
                return $this->_post->getData($key);
            return $default;
        }
        
        /**
         * Check if post data was sent
         */
        
        public function hasPost($key) {
            return $this->getPost($key, false) !== false;
        }
        
        /**
         * Get header option
         * @param string $header_name
         * @return string
         */
        
        public function getHeader($header_name) {
            $key = "HTTP_" . str_replace('-','_', strtoupper($header_name));
            if(isset($_SERVER[$key]))
                return $_SERVER[$key];
            return null;
        }
        
        /**
         * @todo May be have a seanse to filter data
         */
        
        public function getPublicData() {
        	return $this->getData();
        }
        
        /**
         * 
         * Enter description here ...
         */
         
		public function isAjax() {
			return $this->getParam('ajax');
		}

    }
