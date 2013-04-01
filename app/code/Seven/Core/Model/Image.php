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

class Core_Model_Image extends Core_Model_File {
	
	public function isImage() {
		return $this->isFile();
	}
	
	public function getWidth() {
		if(is_readable($this->getPath())) {
			$size = getimagesize($this->getPath());
			if(isset($size[0]))
				return $size[0];
		}
		return null;
	}
	
	public function getHeight() {
		if(is_readable($this->getPath())) {
			$size = getimagesize($this->getPath());
			if(isset($size[1]))
				return $size[1];
		}
		return null;
	}
	
	public function getThumbUrl($width, $height = null, $args = array()) {
		return $this->_getResource()->getThumbUrl($this, $width, $height, $args);
	}

}