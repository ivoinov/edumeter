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

    class Core_Model_Form_Input_Image extends Core_Model_Form_Input_File {
        
        protected $_file_model = 'core/image';
        
        public function __construct($data = array()) {
            parent::__construct($data);
            $this->addValidator('maxsize', array($this, '_isValidMaxSize'));
            $this->addValidator('extension', array($this, '_isValidExtension'));
        }
        
        public function setFile($file){
            if($file instanceof Core_Model_Request_File)
                $file = $file->getAsImage();
            return parent::setFile($file);
        }
        
    }