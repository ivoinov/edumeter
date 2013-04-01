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

class Core_Model_Form_Input_File extends Core_Model_Form_Input_Abstract {
	
	protected $_file_model = 'core/file';
	
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->addValidator('maxsize', array($this, '_isValidMaxSize'));
		$this->addValidator('extension', array($this, '_isValidExtension'));
	}
	
	public function load() {
		$this->_loadIsDefault();
        if(!$this->isSubmit())
            return;
        $file = ($this->getUseDefault() === true) ? NULL : $this->_getRequestValue();
		if($file && is_object($file) && $file->getError() != 4) {
			$this->addData(array('old_value' => $this->getValue(), 'old_file' => $this->getFile()));
			$this->setFile($file);
			parent::setValue(null);
		}
		
		$this->setDeleteFile(Seven::app()->getRequest()->getPost($this->getName() . "_delete"));
	}
	
	public function getSource() {
		return Seven::app()->getRequest()->getFile();
	}
	
	public function _isValidMaxSize() {
		if(! $this->getFile())
			return true;
		if($this->getMaxsize() && $this->getFile()->getSize() > $this->getMaxsize()) {
			$this->setErrorMessage(__("Incorrect size"));
			$this->setErrorCode(1);
			return false;
		}
		return true;
	}
	
	public function _isValidExtension() {
		if(! $this->getFile())
			return true;
		if($this->getExtension() && ! preg_match($this->getExtension(), $this->getFile()->getExtension())) {
			$this->setErrorMessage(__("Incorrect file extension"));
			$this->setErrorCode(1);
			return false;
		}
		return true;
	}
	
	public function setValue($file_id) {
		if($file_id instanceof Core_Model_Request_File) {
			$this->setFile($file_id);
		} else {
			$this->setFile(Seven::getModel($this->_file_model)->load($file_id));
		}
		return parent::setValue(str_replace(BP . DS, '', $this->getFile()->_getId()));
	}
	
	public function save() {
		if($this->getFile()) {
			if($this->getOldFile() && ($this->getOldFile()->_getId() == $this->getOldValue()))
				$this->getOldFile()->remove();
			else 
				Seven::getModel($this->_file_model)->load($this->getOldValue())->remove();
			if($this->getDeleteFile()) {
				if($this->getFile()->_getId())
					$this->getFile()->remove();
				parent::setValue(NULL);
			} else {
				if($this->getFile()->getName()) { // if file really selected
					if(!$this->getIsPrivate()) 
						$this->getFile()->saveAsPublic(); 
					else 
						$this->getFile()->saveAsPrivate();
					$this->setValue($this->getFile()->_getId());
				}
			}
		}
	}
	
	public function setFile($file) {
		if($file instanceof Core_Model_Request_File)
			$file = $file->getAsFile();
		return parent::setFile($file);
	}

}
