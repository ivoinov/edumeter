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

    class Core_Resource_Website extends Core_Resource_Entity {
        
    	protected $_alias  = "core/website";
    	protected $_key    = "id";
    	protected $_table  = "core_website";
    	
        public function getAreaCounters() {
            $areas = Seven::getObjectCache(__CLASS__ . "::" . __METHOD__);
            if(!$areas)
                $areas = Seven::getDatabaseAdapter()
                            ->select($this->getTable(), false)
                            ->group('area')
                            ->columns(array('area', 'count' => 'COUNT(*)'))
                            ->fetchPairs();
            return $areas;
        }
        
        public function isValidCode($code) {
            static $codes = null;
        	if(!$codes)
                $codes = $this->getConnection()
                            ->select($this->getTable(), false)
                            ->columns(array('code', 'id'))->fetchPairs();
            return isset($codes[$code]);        	
        }
        
    }