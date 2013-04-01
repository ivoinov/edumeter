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

	class Seven_Db_Select extends Zend_Db_Select {
	
		public function load($bind = array()) {
			return $this->getAdapter()->fetchAll($this, $bind);
		}
		
		public function fetchPairs($bind = array()) {
			return $this->getAdapter()->fetchPairs($this, $bind);
		}
		
		public function fetchOne($bind = array()) {
			return $this->getAdapter()->fetchOne($this, $bind);
		}
	
		public function fetchRow($bind = array()) {
			return $this->getAdapter()->fetchRow($this, $bind);
		}
		
		public function fetchCol($bind = array()) {
			return $this->getAdapter()->fetchCol($this, $bind);
		}
		
		public function joinl($name, $cond, $cols = self::SQL_WILDCARD, $schema = null) {
			return $this->joinLeft($this->_getTableName($name), $this->_arrayJoinCondition($cond), $cols, $schema);
		}
		
		public function joini($name, $cond, $cols = self::SQL_WILDCARD, $schema = null) {
			return $this->joinInner($this->_getTableName($name), $this->_arrayJoinCondition($cond), $cols, $schema);
		}

		public function joinr($name, $cond, $cols = self::SQL_WILDCARD, $schema = null) {
			return $this->joinRight($this->_getTableName($name), $this->_arrayJoinCondition($cond), $cols, $schema);
		}
		
		public function order($key, $order = null) {
			if(is_array($key) && $order === null) {
				foreach($key as $index => $asc) {
					$this->order((is_numeric($index) ? '' : ($index . ' ')) . $asc);
				}
				return $this;
			}
			return parent::order($key, $order);
		}
		
		public function from($name, $cols = '*', $schema = null) {
			return parent::from($this->_getTableName($name), $cols, $schema);	
		}
		
		public function filter($field, $conditions) {
			$condition = new Seven_Db_Expr($field, $conditions, $this->getAdapter());
			return $this->where($condition->toString());
		}
		
		public function orFilter($field, $conditions) {
			$condition = new Seven_Db_Expr($field, $conditions, $this->getAdapter());
			return $this->orWhere($condition->toString());
		}
		
		protected function _arrayJoinCondition($conditions) {
			if(!is_array($conditions)) 
				return $conditions;
			$cond_c = array();
			foreach($conditions as $key => $value) {
				if(is_numeric($key)) {
					$cond_c[] = $value;
				} else { 
					$cond_c[] = $this->getAdapter()->quoteIdentifier($key) . ' = ' . $this->getAdapter()->quote($value);
				}
			}
			return implode(' AND ', $cond_c);			
		}
		
		protected function _getTableName($name) {
			if(is_string($name) && strpos($name, ':') !== false) {
				list($name, $alias) = explode(':', $name, 2);
				$name = array($alias => $name);
			}
			return $name;
		}
		
		/**
		 * @deprecated since 0.9.0, use columns() instead
		 */
		
		public function result($columns) {
			return $this->columns($columns);
		}
			
	}