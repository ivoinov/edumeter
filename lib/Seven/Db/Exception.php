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

	class Seven_Db_Exception extends Zend_Db_Exception {
		
		protected $_sql = null;
		
		public function getSqlQuery() {
			return $this->_sql; 
		}
		
		public function setSqlQuery($sql) {
			$this->_sql = $sql;
			return $this; 
		}
		
		public function __construct($message, $code = null, $previous = null, $sql_query = null) {
			parent::__construct($message, $code, $previous);
			$this->setSqlQuery($sql_query);
		}
		
	}