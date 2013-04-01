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

class Core_Resource_Entity_Collection extends Core_Resource_Abstract_Collection {
    
    protected $_alias;
    
    protected $_select;
    
    protected $_loaded = false;

    /**
     * 
     * @return Seven_Db_Select
     */
    
    public function getSelect() {
        if($this->_select === NULL) {
            $this->_select = $this->getResource()->getConnection()
            					->select($this->getTable() . ":main");
        }
        return $this->_select;        
    }

    public function where($key, $value = NULL){;
		$this->_loaded = false;
    	$this->getSelect()->where($key, $value);
        return $this;
    }

    public function filter($key, $condition) {
    	$this->_loaded = false;
    	$this->getSelect()->filter($key, $condition);
    	return $this;
    }
    
    public function orFilter($key, $condition) {
		$this->_loaded = false;
    	$this->getSelect()->orFilter($key, $condition);
        return $this;
    }
    
    public function order($order) {
		$this->_loaded = false;
    	$args = func_get_args();
        call_user_func_array(array($this->getSelect(), 'order'), $args);
        return $this;
    }
    
    public function load($force = false) { 
    	if($this->_loaded && !$force)
    		return $this;
    	$this->_loaded = true;
        $raw = $this->getSelect()->load();
        $collection = array();
        foreach($raw as $item) {
            $object = $this->_createModel($item);
        	if(Seven::getConfig('resources/identity_map', 'system') && $this->getUseIdentityMap() !== false && isset($item[$this->getKey()])) {
                $id = $this->getResource()->_getIdentityId($object, $item[$this->getKey()]);
                $identity_map = Seven::getObjectCache($this->getAlias() . '::identity_map');
                if(array_key_exists($id, (array)$identity_map))
                    $object = $identity_map[$id];
                else
                    $identity_map[$id] = $object;
            }
            $collection[] = $object;
        }
        $this->exchangeArray($collection);
        return $this;
    }
    
    public function _createModel($data) {
    	return Seven::getModel($this->getAlias())->loadData($data);
    }
    
    public function getResource() {
    	return Seven::getResource($this->getAlias());
    }
    
    public function getTable() {
		return $this->getResource()->getTable();
    }
    
    public function getAlias() {
        if($this->_alias === NULL)
            $this->_alias = preg_replace('/_collection$/', "", Seven::getAliasByClass(get_class($this), "resource"));
        return $this->_alias;
    }

    public function getKey() {
		return $this->getResource()->getKey();
    }    
    
    protected $_use_identity_map;
    
    public function setUseIdentityMap(bool $use) {
        $this->_use_identity_map = $use;
        return $this;
    }
    
    public function getUseIdentityMap() {
        return $this->_use_identity_map;
    }
    
    public function getIterator() {
    	$this->load();
    	return parent::getIterator();
    }
    
    public function count() {
    	$this->load();
    	return parent::count();
    }

    public function offsetGet($offset) {
    	$this->load();
    	return parent::offsetGet($offset);
    }
    public function offsetExists($offset) {
    	$this->load();
    	return parent::offsetExists($offset);
    }
    
    public function limit($limit, $offset = 0) {
    	$this->getSelect()->limit($limit, $offset);
    	return $this;
    } 
    
    public function countAll() {
		$select = clone $this->getSelect();
		//$select->resetJoins();
		$select->reset(Zend_Db_Select::COLUMNS);
		$select->columns(array('records' => 'count(*)'));
		$records = $select->getPart(Zend_Db_Select::GROUP) ? count($select->load()) : $select->fetchOne();
		return $records;
    }
    
}
