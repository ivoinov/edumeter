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

    class Core_Model_Response {
        
        private $_headers = array();
        private $_body = "";
        private $_ajax = null;
        private $_code = 200;   
        private $_code_message;
        static  $_code_descriptions = array(
                    200 => 'OK',
                    404 => 'Page not found',
                    503 => 'Service Unavailable ',
                    500 => 'Internal Server Error',
                    403 => 'Forbidden',
                    400 => 'Bad Request',
                    307 => 'Temporary Redirect',
                    301 => 'Moved Permanently',
                    302 => 'Found',
                    303 => 'See Other',
                    304 => 'Not Modified',
                );
        
        /**
         * Send headers if they not sent 
         */
        
        function send() {
        	Seven::event('before_response_header_send', array('response' => $this));
            if(!$this->isHeadersSent()) {
                $this->sendHeaders();
            }
            Seven::event('before_response_body_send', array('response' => $this));
            if($this->isAjax() && $this->getAjaxData()) {
            	echo json_encode($this->getAjaxData());
            } else {
            	echo $this->getBody();
            }
            flush();         
        }
        
        /**
         * Get header for name
         * 
         * @param string $name
         * @return mixed
         */
        
        function getHeader($name) {
            if (isset($this->_headers[$name])) return $this->_headers[$name];
            return null;
        }
        
        /**
         * Set header name and value 
         * 
         * @param string $name
         * @param mixed $content
         * @return Core_Model_Response 
         */
        
        function setHeader($name, $content) {
            $this->_headers[$name] = $content;
            return $this;
        }
        
        /**
         * Check if headers sent
         * 
         * @return type 
         */
        
        function isHeadersSent() { return headers_sent(); }
        
        /**
         * Send all headers 
         * 
         * @return Core_Model_Response 
         */
        
        function sendHeaders() {
            $this->_sentHttpCode();
            foreach ( $this->_headers as $name => $content) {
                header($name.': '.$content);
            }
            return $this;
        }
        
        /**
         * Set body content
         * 
         * @param string $content
         * @return Core_Model_Response 
         */
        
        function setBody($content) {
            $this->_body = $content;
            return $this;
        }
        
        /**
         *  Add content to body
         * 
         * @param string $content
         * @return Core_Model_Response 
         */
        
        function addBody($content) {
            $this->_body .= $content;
            return $this;
        }
        
        /**
         * Get body content
         * 
         * @return string
         */
        function getBody() {
            return $this->_body;
            
        }
        
        /**
         * Redirect to another location and call exit() 
         * 
         * @param string $location
         * @param int $code 
         */
        
        function redirect($location, $code = 302) {
        	if(!$this->getIsAjax()) {
	            $this->setCode($code);
	            $this->setHeader('Location', $location);
	            $this->setBody('');
        	} else {
        		$this->setBody(json_encode(array('code' => $code, 'location' => $location)));
        	}
	        $this->send();
        	exit();
        }
        
        /**
         * initialize http code and message 
         * 
         * @param int $code
         * @param string $message
         * @return Core_Model_Response 
         */
        
        function setCode($code, $message = NULL) {
            $this->_code = $code;
            
            if($message) {
                $this->_code_message = $message;
            }else { 
                if(!empty(self::$_code_descriptions[$code])) {
                    $this->_code_message = self::$_code_descriptions[$code];
                }else { 
                    $this->_code_message = "";
                }
            }
            return $this;
        }
        
        /**
         *  sent http header with code and message 
         */
        
        private function _sentHttpCode() {
            header('HTTP/1.0 '.$this->_code.' '.$this->_code_message);
        }
        
        public function init() {
        }
        
        protected $_is_ajax = false;
        
        public function isAjax() {
        	return $this->_is_ajax;
        }
        
        public function getIsAjax() {
        	return $this->isAjax();
        }
        
        public function setIsAjax($set) {
        	$this->_is_ajax = $set;
        	return $this;
        }
        
        public function addAjaxData($key, $value) {
        	$this->_ajax[$key] = $value;
        	return $this; 
        }
        
        public function setAjaxData($key, $value = null) {
        	if(is_array($key) && $value === null)
        		$this->_ajax = $key;
        	$this->addAjaxData($key, $value);
        	return $this; 
        }
        
        public function unsetAjaxData($key, $value = null) {
        	unset($this->_ajax[$key]);
        	return $this; 
        }
        
        public function getAjaxData($key = null) {
        	if($key === null)
        		return $this->_ajax;
        	return isset($this->_ajax[$key]) ? $this->_ajax[$key] : null;
        }
        
    }