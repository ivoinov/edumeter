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

	class Seven_Smartstring {
		
		protected $_string = "";
		protected $_callbacks = array();
		
		public function __construct($string, array $callbacks = array()) {
			$this->_string = $string;
			$this->_callbacks = $callbacks;
		}
		
		public function setString($string) {
			$this->_string = $string;
			return $this;
		}
		
		public function getString() {
			return $this->_string;
		}
		
		public function addCallback($callback) {
			$this->_callbacks[] = $callback;
			return $this;
		}
		
		public function __toString() {
			$string = $this->_string;
			foreach($this->_callbacks as $callback) {
				$string = (string)call_user_func($callback, $string);
			}
			return $string;
		}
		
	}