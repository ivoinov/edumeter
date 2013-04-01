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

class Core_Model_Request_File extends Core_Model_Abstract {
	
	/**
	 * create model object init it and return object
	 * 
	 * @return Core_Model_File
	 */
	
	public function getAsFile() {
		if($this->getError() != 0)
			throw new Exception("File upload error: {$this->getError()} ");
		
		return Seven::getModel('core/file')->loadFromRequest($this);
	}
	
	/**
	 * create model object init it and return object
	 * 
	 * @return Core_Model_Image
	 */
	
	public function getAsImage() {
		if($this->getError() != 0)
			throw new Exception("Image upload error: {$this->getError()} ");
		
		return Seven::getModel('core/image')->loadFromRequest($this);
	}

}