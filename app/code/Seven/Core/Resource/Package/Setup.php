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

class Core_Resource_Package_Setup extends Seven_Object {

	protected $_use_trasaction = false;
    
    /**
     * Create new Seven_Database_Mysql5_Adapter_Table
     *
     * @param string $table_name table name
     * @param array $fields table fields
     * @return Seven_Database_Mysql5_Adapter_Table
     */ 
    
    public function createTable($table_name, $fields = array()) {
        return new Seven_Database_Mysql5_Adapter_Table(Seven::getDatabaseAdapter(), $table_name, $fields);
    }
 
    /**
     * Make a database query
     *
     * @param string $sql sql query
     * @return resource query result
     */ 
    
    public function query($sql, $bind = array()) {
        return Seven::getDatabaseAdapter()->query($sql, $bind);
    }

    /**
     * Start transaction
     *
     * @return Core_Resource_Setup
     */ 
    
    public function start() {
    	if(!Seven::getDatabaseAdapter()->inTransaction()) {
    		Seven::getDatabaseAdapter()->beginTransaction();
    		$this->_use_trasaction = true;
    	}
        return $this;
    }

    /**
     * Successfully end transaction
     *
     * @return Core_Resource_Setup
     */ 
    
    public function end() {  
    	if($this->_use_trasaction) {
    		Seven::getDatabaseAdapter()->commit();
    		$this->_use_trasaction = false;
    	}
        return $this;
    }
    
    /**
     * Unsuccessfully end transaction
     *
     * @return Core_Resource_Setup
     */ 
    
    public function rollback() {
    	Seven::getDatabaseAdapter()->rollBack();
    	if(!$this->_use_trasaction)
    		throw new Core_Exception_Error('Unable to complete install script');
    	$this->_use_trasaction = false;
        return $this;
    }
    
    
    /**
     * Include php file
     *
     * @return Core_Resource_Setup
     */     
    
    public function run() {
        try {
            include($this->getPath());
        }
        catch (Exception $ex) {
            Seven::log(E_ERROR, $ex->getMessage());
            if($this->_use_trasaction)
            	$this->rollback();
            throw $ex;
        }
        return $this;
    }
    
    public function getTable($table_name) {
        return Seven::getDatabaseAdapter()->getTable($table_name);       
    }
    
}