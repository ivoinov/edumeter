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

    class Core_Resource_Image extends Core_Resource_File {

        public function getThumbUrl($object, $width, $height = null, $args = array()) {
            if(!$object->getPath())
                return NULL;
            if($width == $object->getWidth() && $height == $object->getHeight() && $args === array())
                return $object->getUrl();
            try {
	            $target_dir = str_replace(BP . DS, '', $this->_getThumbPath($object));
	
	            if(!is_dir(BP . DS . $target_dir))
	                mkdir(BP . DS . $target_dir, 0777, true);
	            $args['width'] = $width;
	            $args['height'] = $height;
	            ksort($args);
	            $target_path = $target_dir . DS . md5(http_build_query($args)) . "." . $object->getExtension();
	            if(!file_exists($target_path)) {
	                $processor = new Seven_Image_Adapter($object->getPath());
	                $processor->resize($args)
	                          ->save($target_path)
	                          ->close();
	            }
	            return Seven::app()->getRequest()->getBaseUrl() . "/" . str_replace(DS, "/", $target_path);
            } catch(Exception $e) {
				Seven::log(E_WARNING, $e);
            }
            return $object->getUrl();
        }

        public function remove(&$object) {
            if($object->getPath() && $object->getName() && is_dir($target_path = $this->_getThumbPath($object))) {
                $thumbs = glob($target_path . DS . "*");
                foreach($thumbs as $thumb)
                    unlink($thumb);
                rmdir($target_path);
            }
            parent::remove($object);
        }

        protected function _getThumbPath($object) {
            return dirname($object->getPath()) . DS . str_replace('.' . $object->getExtension(), '', $object->getName());
        }
                
    }
