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

    abstract class Seven_Image_Adapter_Abstract {
        
        protected $_filename;
        
        abstract public function getWidth();
        abstract public function getHeight();
        
        public function __construct($filename = null) {
            if($filename)
                $this->open($filename);
        }
        
        public function open($filename) {
            $this->_filename = $filename;
            $this->_open($filename);
            return $this;
        }
        
        abstract protected function _open($filename);
        abstract public function close();
        abstract public function save($filename = null);
        
        abstract protected function _place($left, $top, $width, $height, $new_width, $new_height, $new_background = null);
        
        public function resize($args) {
            if(!($args instanceof Seven_Object))
                $args = new Seven_Object($args);
            if(!$args->getWidth() && !$args->getHeight()) // nothing to resize
                return $this;
            if(!$this->getWidth() || !$this->getHeight()) // nothing to resize
                return $this;
            if($args->getHeight() && !$args->getWidth())
                $args->setHeight($this->getHeight() * ($args->getWidth() / $this->getWidth()));
            if($args->getWidth() && !$args->getHeight())
                $args->setWidth($this->getWidth() * ($args->getHeight() / $this->getHeight()));
            
            $x_ratio = $args->getWidth() / $this->getWidth();
            $y_ratio = $args->getHeight() / $this->getHeight();
            
            switch(strtolower($args->getMode())) {
                case 'fill':
                    $this->_place(0, 0, $args->getWidth(), $args->getHeight(), $args->getWidth(), $args->getHeight(), $args->getBackground());
                    break;
                case 'scale':
                    if($x_ratio < $y_ratio)
                        $this->_place(0, ($args->getHeight() - $this->getHeight() * $x_ratio) / 2, $args->getWidth() * $x_ratio, $args->getHeight() * $x_ratio, $args->getWidth(), $args->getHeight(), $args->getBackground());
                    else
                        $this->_place(($args->getWidth() - $this->getWidth() * $y_ratio) / 2, 0, $args->getWidth() * $y_ratio, $args->getHeight() * $y_ratio, $args->getWidth(), $args->getHeight(), $args->getBackground());
                    break;
                default:
                    if($x_ratio > $y_ratio)
                        $this->_place(0, ($args->getHeight() - $this->getHeight() * $x_ratio) / 2, (int)$this->getWidth() * $x_ratio, (int)$this->getHeight() * $x_ratio, $args->getWidth(), $args->getHeight(), $args->getBackground());
                    else
                        $this->_place(($args->getWidth() - $this->getWidth() * $y_ratio) / 2, 0, (int)$this->getWidth() * $y_ratio, (int)$this->getHeight() * $y_ratio, $args->getWidth(), $args->getHeight(), $args->getBackground());
                    break;
            }
            return $this;
        }
        
    }
    