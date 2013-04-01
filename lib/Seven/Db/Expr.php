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

	class Seven_Db_Expr extends Zend_Db_Expr {
		
		protected $_conditions = array();
		
		public function __construct($field, $condition = null, $adapter = null) {
			$this->setFieldname($field);
			
			if(is_array($condition)) {
				$this->_conditions = array_merge($this->_conditions, $condition);
			} else {
				$this->addCondition('is', $condition);
			}
			
			$this->setAdapter($adapter);
		}
		
		public function addCondition($type, $value) {
			$this->_conditions[$type] = $value;	
			return $this;		
		}
		
		protected $_adapter = null;
	
		public function getAdapter() {
			return $this->_adapter ? $this->_adapter : self::getGlobalAdapter();
		} 
		
		public function setAdapter($adapter) {
			$this->_adapter = $adapter;
			return $this;
		}
		
		public function getFieldname() {
			return $this->_expression;
		} 
		
		public function setFieldname($field) {
			$this->_expression = $field;
			return $this;
		}
		
		public function toString() {
			return $this->_getExpression($this->_expression, $this->_conditions);
		}
		
		public function __toString() {
			try {
				return $this->toString();
			} catch(Exception $e) {
				echo $e;
			}
			return "";
		}
			
		protected function _getExpression($key, $conditions) {
			if(strpos($key, '`') === false) // TODO: Quick fix. Think about it
				$key = $this->getAdapter()->quoteIdentifier($key);
			if(! is_array($conditions))
				$conditions = array('is' => $conditions);
			$where = array();
			foreach($conditions as $condition => $value) {
				if(! is_array($value))
					$value = $this->getAdapter()->quote($value);
				
				switch($condition) {
					case "is":
					case "eq":
						$where[] = $key . " = " . $value;
						break;
					case "neq":
						$where[] = $key . " != " . $value;
						break;
					case "from":
					case "he":
						$where[] = $key . " >= " . $value;
						break;
					case "to":
					case "le":
						$where[] = $key . " <= " . $value;
						break;
					case "hi":
						$where[] = $key . " > " . $value;
						break;
					case "lw":
						$where[] = $key . " < " . $value;
						break;
					case "like":
						$where[] = $key . " LIKE " . $value;
						break;
					case "regexp":
						$where[] = $key . " REGEXP " . $value;
						break;
					case "in":
						$where[] = empty($value) ? "0" : ($key . " IN(" . implode(",", array_map(array($this->getAdapter(), 'quote'), $value)) . ")");
						break;
					case "nin":
					case "not in":
						$where[] = empty($value) ? "0" : ($key . " NOT IN(" . implode(",", array_map(array($this->getAdapter(), 'quote'), $value)) . ")");
						break;
					case "null":
						$where[] = $key . " IS " . ($value ? "" : "NOT ") . "NULL";
						break;
					default:
						throw new Zend_Db_Exception("Unknown condition '" . $condition . "'");
				}
			}
			return "(" . implode(" AND ", $where) . ")";		
		}
		
	}