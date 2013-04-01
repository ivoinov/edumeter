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

    class Core_Model_Url extends Core_Model_Abstract {
        
        /**
         *
         * @param string $url
         * @param array $args additional parameters to request
         * @return Core_Model_Url 
         */
        
        function parse($url, $args = array()) {
            $defaults = array('port' => 80, 'host' => 'localhost', 'path' => '', "script_name" => "index.php", 'scheme' => 'https');              // default values
            $defaults = array_intersect_key(Seven::app()->getRequest()->getData(), $defaults);
            // reset URL data
            $this->setData($defaults);
            $parameters = array();
            // fill data from URL
            $parse_url = parse_url($url);
            if(isset($parse_url['scheme'])) $this->setScheme($parse_url['scheme']);
            if(isset($parse_url['host']))   $this->setHost($parse_url['host'])->setPath('')->setPort(80);
            if(isset($parse_url['port']))   $this->setPort($parse_url['port']);
            if(isset($parse_url['query'])) parse_str($parse_url['query'], $parameters);
            if(!empty($args)) $parameters = array_merge_recursive_replace($parameters, $args);
            $this->setParameters($parameters);
            $this->setRequest(empty($parse_url['path']) ? "" : ltrim($parse_url['path'], '/'));
            return $this;
        }
        
        /**
         * Check scheme
         * 
         * @return bool
         */
        
        function isSecure() {
            if(strcasecmp($this->getScheme(), 'https') == 0) return true;
            return false;
        }
        
        /**
         * Get request parameter
         * 
         * @param type $search_key request parameter name
         * @return mixed 
         */
        
        function getParam($search_key, $default = NULL) {
            if(is_string($search_key)){
                $parameters = $this->getParameters();
                if(isset($parameters[$search_key])){
                    return $parameters[$search_key];
                }
            }
            return $default;
        }
        
        /**
         * Set request parameter
         * 
         * @param string $key
         * @param mixed $value
         * @return Core_Model_Url 
         */
        
        function setParam($key, $value) {
            $this->setData('parameters',array_merge($this->getParameters(), array($key=>$value)));
            return $this;
        }  
        
        /**
         * Delete request paramenter
         * 
         * @param string $key
         * @return Core_Model_Url 
         */
        
        function unsParam($key) {
            if(is_string($key)) {
                $param = $this->getParameters();
                if(isset($param[$key])) {
                    unset($param[$key]);
                    $this->setData('parameters', $param);
                }
            }
            return $this;
        }
        
        /**
         * Check is exist request parameter
         * 
         * @param string $key
         * @return bool 
         */
        
        function hasParam($key) {
            $parameters = $this->getParameters();
            foreach ($parameters as $k => $value) {
                if($k == $key) return true;
            }
            return false;
        }
        
        /**
         * Get url to string
         *
         * @return string 
         */

        public function getBaseUrl() {
            $url = $this->getScheme().'://';
            $url .= $this->getHost();
            if($this->getPort() !== null && $this->getPort() != 80 ){
                $url .=':'.$this->getPort();
            }

            if($this->getPath())
                $url .= "/" . $this->getPath();
            return $url;
        }

        /**
         * Assambly URL to string
         */
        
        function toString() {
            $url = $this->getBaseUrl() . "/" . $this->getRequest();
            
            if($this->getParameters()!= null)
                $url .= "?" . http_build_query($this->getParameters());
            
            return $url;
        }
   
        function __toString() {
            return $this->toString();
        }
    }
        