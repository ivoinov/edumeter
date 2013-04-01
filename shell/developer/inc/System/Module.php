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
 */

    class System_Module extends Seven_Object {
    
		public function create() {
		    if(!$this->getName())
				throw new Exception("Module name is not specified");
		    if(!$this->getPool())
				throw new Exception("Module pool is not specified");
		    
		    $this->setFullname(($this->getPool() == 'Seven') ? $this->getName() : ($this->getPool() . "_" . $this->getName()));
		    
		    if(is_dir(BP . DS . "app" . DS . "code" . DS . $this->getPool() . DS . $this->getName()))
				throw new Exception("Module folder already exists");
	
		    $module_home = BP . DS . "app" . DS . "code" . DS . $this->getPool() . DS . $this->getName();
	
		    // create module dir
		    if(!mkdir($module_home, 0755, true))
				throw new Exception("Can't create module folder");
			
		    foreach(explode("\n", file_get_contents($this->getEtcPath() . "folders.template")) as $path) {
		    	if(!trim($path)) continue;
				if(!mkdir($module_home . DS . trim($path = str_replace(array("\\", "/"), DS, trim($path)), DS), 0755, true))
				    throw new Exception("Can't create folder {$path}");
		    }
		    
		    
	//	    if(!file_put_contents(BP . DS . "app" . DS . "etc" . DS . "modules" . DS . $this->getPool() . "_" . $this->getName() . ".xml", $this->_getTemplateContent("module.xml.template")))
	//			throw new Exception("Can't create config");
	
		    if(!file_put_contents($module_home . DS . "etc" . DS . "config.xml", $this->_getTemplateContent("config.xml.template")))
				throw new Exception("Can't create module config");
	
		    if($this->getGenerateCrud()) {
		    	$this->addData('crud_entity_fullname', strtolower($this->getName()) . '/' . $this->getCrudEntityName());
		    	$this->addData('crud_entity_poolname', strtolower($this->getName()));
		    	if(!file_put_contents($module_home . DS . "etc" . DS . "routing.xml", $this->_getTemplateContent("routing.xml.template")))	
		    		throw new Exception("Can't create entity config file");
		    	
		    	if(!file_put_contents($module_home . DS . "etc" . DS . "entity-" . $this->getCrudEntityName() . ".xml", $this->_getTemplateContent("entity-crud.xml.template")))	
		    		throw new Exception("Can't create entity config file");
		    }
		    
		}
		
		protected function _getTemplateContent($template) {
		    $content = file_get_contents($this->getEtcPath() . $template);
		    return preg_replace_callback('/\{\{([A-Z_]+)\}\}/', array($this, '_templateReplaceCallback'), $content);
		}
		
		protected function _templateReplaceCallback($matches) {
		    return $this->getData(strtolower($matches[1]));
		}
		
		public function load($name) {
		    $this->setName($name);
		    $this->setPool($pool = Seven::getConfig("modules/{$name}/pool"));
		    if(!$pool)
			throw new Exception("Module '{$name}' is not found");
		    $module_home = BP . DS . "app" . DS . "code" . DS . $this->getPool() . DS . $this->getName();
		    if(!is_dir($module_home))
			throw new Exception("Module '{$name}' home folder is not exists");
		}
		
		public function delete() {
		    $module_home = BP . DS . "app" . DS . "code" . DS . $this->getPool() . DS . $this->getName();
		    $this->_rmdir_recursive($module_home);
		    unlink(BP . DS . "app" . DS . "etc" . DS . "modules" . DS . $this->getPool() . "_" . $this->getName() . ".xml");
		}
		
		protected function _rmdir_recursive($path) {
		    //echo "Delete " . $path . "\n";
		    if(!is_dir($path)) return;
		    $d = dir($path);
		    while (false !== ($entry = $d->read())) {
			if(basename($entry) == "." || basename($entry) == "..") continue;
			if(is_dir($path . DS . $entry)) 
			    $this->_rmdir_recursive($path . DS . $entry);
			else
			    unlink($path . DS . $entry);
		    }
		    $d->close();
		    rmdir($path);
		}
    
    }