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

    class Core_Model_File extends Core_Model_Entity {

    	public function isFile() {
    		return is_file($this->getPath());
    	}
    	
    	public function canRead() {
    		return is_readable($this->getPath());
    	}
    	
    	public function canUpdate() {
    		return is_writeable($this->getPath());
    	}
    	
    	public function canDelete() {
    		return is_writeable(dirname($this->getPath()));
    	}
    	
    	public function canCreate() {
    		return is_writeable(dirname($this->getPath()));
    	}
    	
    	public function getExtension() {
    		$part = explode('.', $this->getName());
    		end($part);
    		return $part[key($part)];
    	}
    	
        public function getUrl() {
            return Seven::app()->getRequest()->getBaseUrl() . "/" . trim(str_replace(DS, "/", str_replace(BP, "", $this->getPath())), '/');
        }

        public function __toString() {
            return $this->getUrl();
        }
        
        public function getContent() {
        	if($this->_getId())
        		return file_get_contents($this->_getId());
        }
        
        public function loadFromRequest(Core_Model_Request_File $file) {
        	$this->load($file->getTmpName());
        	$this->setName($file->getName());
        	return $this;
        }

        public function saveAsPublic() {
        	$path = $this->getName() . "__";
        	$this->setDirName(BP . DS . 'public' . DS . 'media' . DS . $path{0} . DS . $path{1});
        	return $this->save();
        }

        public function saveAsPrivate() {
        	$path = $this->getName() . "__";
        	$this->setDirName(BP . DS . 'var' . DS . 'media' . DS . $path{0} . DS . $path{1});
        	return $this->save();
        }
        
    }
