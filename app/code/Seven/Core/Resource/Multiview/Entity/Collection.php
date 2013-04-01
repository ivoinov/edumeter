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

class Core_Resource_Multiview_Entity_Collection extends Core_Resource_Entity_Collection {
	
	protected $_view_id = false;
	
	public function _setView($view_id) {
		$this->_view_id = $view_id;
		return $this;
	}
	
	public function _getView() {
		if($this->_view_id === false)
			$this->_view_id = Seven::getModel($this->getAlias())->_getDefaultView();
		return $this->_view_id;
	}
	
	public function _createModel($data) {
		return Seven::getModel($this->getAlias())->_setView($this->_getView())->loadData($data);
	}
	
	public function getSelect() {
		if($this->_select === NULL) {
			$object = Seven::getModel($this->getAlias());
			$this->_select = parent::getSelect();
			// JOIN global data
			if($this->getViewsTable()) { // if using two tables
				$this->_select->joini(
					$this->getViewsTable() . ":global", 
					array(
						'global._view_id' => (string)$object->_getDefaultView(), 
						'global.' . $this->getKey() . ' = main.' . $this->getKey()
					),
					false
				);
				$prefix = 'global';
			} else {
				$this->_select->filter('main._view_id', (string)$object->_getDefaultView());
				$prefix = 'main';
			}
			// JOIN view data
			$this->_select->joinl(
				($this->getViewsTable() ? $this->getViewsTable() : $this->getTable()) . ":view", 
				array('view._view_id' => (string) $this->_getView(), 'view.' . $this->getKey() . ' = ' . $prefix . '.' . $this->getKey()), 
				false
			);
			// Add all from non-view table
			if($this->getViewsTable())
				$this->_select->columns(array($this->getKey() => 'main.' . $this->getKey()));
			
			// Add expressions for view tables
			$fields = array();
			$columns = $this->getResource()->getConnection()->describeTable($this->getViewsTable() ? $this->getViewsTable() : $this->getTable());
			foreach($columns as $id => $data) {
				if($id == '_view_id' || $id == $this->getKey())
					continue;
				$fields[$id] = new Zend_Db_Expr("IF(view.{$id} IS NULL, {$prefix}.{$id}, view.{$id})");
			}
			$this->_select->columns($fields);
		}
		return $this->_select;
	}
	
	public function filter($key, $condition) {
		return parent::filter($this->_addTableAliasToField($key), $condition);				
	}
	
	public function where($sql, $bind = array()) {
		if($sql instanceof Seven_Db_Expr) {
			$sql->setFieldname($this->_addTableAliasToField($sql->getFieldname()));
		}
		return parent::where($sql, $bind);
	}

	protected function _addTableAliasToField($key) {
		if($key != '_view_id' && $key != $this->getKey() && in_array($key, $this->getViewFields())) {
			$prefix = $this->getViewsTable() ? 'global' : 'main';
			$key = "IF(`view`.`{$key}` IS NULL, `{$prefix}`.`{$key}`, `view`.`{$key}`)";
		} else if(strpos($key, '`') === false) { // TODO: Quick fix. Think about it
			$key = "`main`.`{$key}`";
		}
		return $key;		
	}
	
	public function getViewFields() {
		static $columns = null;
		if($columns === null) {
			$columns = array_keys($this->getResource()->getConnection()->describeTable($this->getViewsTable() ? $this->getViewsTable() : $this->getTable()));
		}
		return $columns;
	}
	
	public function getViewsTable() {
		return Seven::getResource($this->getAlias())->getViewsTable();
	}
	
}