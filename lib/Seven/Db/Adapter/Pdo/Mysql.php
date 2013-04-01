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

class Seven_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Mysql {

	protected $_in_trasaction = false;
	
	public function inTransaction() {
		return $this->_in_trasaction;
	}
	
	public function beginTransaction() {
		$this->_in_trasaction = true;
		return parent::beginTransaction();
	}
	
	public function commit() {
		$this->_in_trasaction = false;
		return parent::commit();
	}
	
	public function rollBack() {
		$this->_in_trasaction = false;
		return parent::rollBack();
	}
	
	/**
	 * Replace Zend_Db_Select to Seven_Db_Select
	 * @see Zend_Db_Adapter_Abstract::select()
	 */
	
	public function select($table = null, $cols = '*') {
		$select = new Seven_Db_Select($this);
		if($table)
			$select->from($table, $cols);
		return $select;
	}

	public function update($table, array $bind, $where = '') {
		$where = $this->_prepareWhere($where);
		return parent::update($table, $bind, $where);
	}
	
	public function delete($table, $where = '') {
		$where = $this->_prepareWhere($where);
		return parent::delete($table, $where);
	}
	
	protected function _prepareWhere($where) {
		if(!is_array($where)) return $where;
		$filters = array();
		foreach($where as $index => $condition) {
			$filters[] = new Seven_Db_Expr($index, $condition, $this);
		} 
		return implode(' AND ', $filters);
	}
	
	/**
	 * Insert record into database and in case of duplicated keys update 
	 * @param string $table
	 * @param array $bind
	 * @throws Zend_Db_Adapter_Exception
	 */
	
	public function place($table, array $bind) {
		// extract and quote col names from the array keys
		$cols = array();
		$vals = array();
		$quotedValues = array();
		$i = 0;
		foreach ($bind as $col => $val) {
			$cols[] = $this->quoteIdentifier($col, true);
			if ($val instanceof Zend_Db_Expr) {
				$vals[] = $val->__toString();
				$quotedValues[] = $val->__toString();
				unset($bind[$col]);
			} else {
				if ($this->supportsParameters('positional')) {
					$vals[] = '?';
				} else {
					if ($this->supportsParameters('named')) {
						unset($bind[$col]);
						$bind[':col'.$i] = $val;
						$vals[] = ':col'.$i;
						$i++;
					} else {
						/** @see Zend_Db_Adapter_Exception */
						require_once 'Zend/Db/Adapter/Exception.php';
						throw new Zend_Db_Adapter_Exception(get_class($this) ." doesn't support positional or named binding");
					}
				}
			}
			$quotedValues[] = end($cols) . " = " . end($vals);
		}

		// build the statement
		$sql = "INSERT INTO "
		. $this->quoteIdentifier($table, true)
		. ' (' . implode(', ', $cols) . ') '
		. 'VALUES (' . implode(', ', $vals) . ') '
		. ' ON DUPLICATE KEY UPDATE ' . implode(', ', $quotedValues);

		// execute the statement and return the number of affected rows
		if ($this->supportsParameters('positional')) {
			$bind = array_merge(array_values($bind), array_values($bind));
		}
		$stmt = $this->query($sql, $bind);
		$result = $stmt->rowCount();
		return $result;
	}

	/**
	 * Replace Zend_Db_Exception to Seven_Db_Exception
	 * @see Zend_Db_Adapter_Pdo_Abstract::query()
	 */
	
	public function query($sql, $bind = array()) {
		try {
			return parent::query($sql, $bind);
		} catch(Zend_Db_Exception $e) {
			throw new Seven_Db_Exception($e->getMessage(), $e->getCode(), $e, $sql);
		}
	} 
	
}