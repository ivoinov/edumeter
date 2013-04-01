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
 *
 */

    class  Seven_Image_Adapter_Gd extends Seven_Image_Adapter_Abstract {
        
        protected $_width, $_height, $_image, $_info, $_mime, $_type;
        
        public function getWidth() {
            return $this->_width;
        }
        
        public function getHeight() {
            return $this->_height;
        }
        
        protected function _open($filename) {
            $this->_info   = GetImageSize($filename);
            $this->_width  = $this->_info[0];
            $this->_height = $this->_info[1];
            $this->_mime   = $this->_info['mime'];

            if ($this->_mime == image_type_to_mime_type(IMAGETYPE_PNG)){
                $this->_image = imageCreateFromPng($filename);
                $this->_type  = IMAGETYPE_PNG;
            } else if ($this->_mime == image_type_to_mime_type(IMAGETYPE_GIF)){
                $this->_image = imageCreateFromGif($filename);
                $this->_type = IMAGETYPE_GIF;
            } else if ($this->_mime == image_type_to_mime_type(IMAGETYPE_JPEG)){
                $this->_image = imageCreateFromJpeg($filename);
                $this->_type = IMAGETYPE_JPEG;
            } else {
                return 0;
            }
            return 1;
        }
        
        public function close() {
        	if($this->_image)
            	imageDestroy($this->_image);
            return $this;
        }
        
        public function save($filename = null) {
            if($filename == "")
                $filename = $this->_filename;
            if($this->_image) {
	            if($this->_type == IMAGETYPE_GIF){
	                imageGif($this->_image, $filename);
	            } else if($this->_type == IMAGETYPE_PNG){
	                imagePng($this->_image, $filename);
	            } else
	                imageJpeg($this->_image, $filename);
            }
            return $this;
        }
        
        protected function _place($left, $top, $width, $height, $new_width, $new_height, $new_background = null) {
        	if(!$this->_image) return false;
            $_tmp_image = imagecreatetruecolor($new_width, $new_height);
            // TODO: fill with new background
            if(!imagecopyresampled($_tmp_image, $this->_image, $left, $top, 0, 0, $width, $height, $this->getWidth(), $this->getHeight()))
                throw new Exception('Gd can\' resize image');
            //imagestring($_tmp_image, 3, 5, 5, "{$this->_width}x{$this->_height}", imagecolorallocate($_tmp_image, 255, 255, 255));
            //imagestring($_tmp_image, 3, 5, 25, "{$left}:{$top} ({$width}x{$height}) -> {$new_width}x{$new_height}", imagecolorallocate($_tmp_image, 255, 255, 255));
            $this->_image  = $_tmp_image;
            $this->_width  = $new_width;
            $this->_height = $new_height;
        }

        
    }