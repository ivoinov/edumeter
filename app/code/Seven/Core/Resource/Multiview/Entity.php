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

class Core_Resource_Multiview_Entity extends Core_Resource_Entity {
		
	protected function _getLoadQuery($object, $id, $id_field) {
		return $this->getCollection()->_setView($object->_getView())->filter($id_field, $id)->getSelect();
	}
	
	public function _getIdentityId($object, $id) {
		return $object->_getView() . '_' . $id;
	}
	
	public function getViewData($object, $view_id) {
		$data = $this->getConnection()
			->select($this->getViewsTable() ? $this->getViewsTable() : $this->getTable())
			->filter('_view_id', $view_id)
			->filter($this->getKey(), $object->_getId())
			->load();
		if(isset($data[0]))
			return $data[0];
		return NULL;
	}
	
	protected $_view_table = null;
	
	public function getViewsTable() {
		if($this->_view_table === NULL) {
			$this->_view_table = Seven::getConfig("entity/" . $this->getAlias() . "/view_table");
		}
		return $this->_view_table;
	}
	
	public function getViews() {
		$options = Seven::getConfig("entity/" . $this->getAlias() . "/views");
		if($options === null)
			throw new Exception('You should specify views node for ' . $this->getAlias());
		if(is_string($options)) {
			if(strpos($options, '::') === false) $options .= "::getOptionsArray";
			$options = call_seven_callback($options, 'model');
		}	
		return $options;
	}
	
	protected function _getInsertQuery($object, $values) {
		if(!$this->getViewsTable())  // new items always creates in default view
			$values['_view_id'] = $object->_getDefaultView();
		return parent::_getInsertQuery($object, $values);
	}
	
	public function getMultiviewFields() {
		return $this->getConnection()->describeTable($this->getViewsTable() ? $this->getViewsTable() : $this->getTable());
	}
	
	protected function _afterInsert($object) {
		if($this->getViewsTable())
			$this->_placeViewData($object, $object->_getDefaultView());
		return parent::_afterInsert($object);
	}
	
	protected function _getUpdateQuery($object, $values) {
		if($this->getViewsTable())
			return parent::_getUpdateQuery($object, $values);
		$values['_view_id'] = $object->_getView();
		$values[$this->getKey()] = $object->_getId();
		return $this->getConnection()->place($this->getTable(), $values);
	}
	
	protected function _afterUpdate($object) {
		if($this->getViewsTable())
			$this->_placeViewData($object);
		return parent::_afterUpdate($object);
	}
	
	protected function _placeViewData($object, $view_id = null) {
		$values = $this->_getFieldsUpdate($object, $this->getViewsTable());
		$values['_view_id'] = $view_id === null ? $object->_getView() : $view_id;
		$values[$this->getKey()] = $object->_getId();
		$this->getConnection()->place($this->getViewsTable(), $values);
		return $this;
	}
	
}