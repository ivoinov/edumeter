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

    class Core_Model_Form_Input_Text extends Core_Model_Form_Input_Abstract{

        public function __construct($data = array()) {
            parent::__construct($data);
        	$this->addValidator('min',    array($this, '_isValidMinlength'))
                 ->addValidator('max',    array($this, '_isValidMaxlength'))
                 ->addValidator('regexp', array($this, '_isValidRegExp'));
        }
        
        public function _isValidRegExp() {
            if(!$this->getRegexp())
                    return true;
            if(preg_match($this->getRegexp(), $this->getValue()))
                    return true;
            else{
                $this->setErrorMessage(__("Incorrect value"));
                $this->setErrorCode(1);
                return false;
            }
        }

        public function _isValidMinlength(){
            if($this->hasMinlength() && strlen($this->getValue()) < $this->getMinlength()){
                $this->setErrorMessage(__("Incorrect length"));
                $this->setErrorCode(2);
                return false;
            }
            return true;
        }

        public function _isValidMaxlength(){
            if($this->hasMaxlength() && strlen($this->getValue()) > $this->getMaxlength()){
                $this->setErrorMessage(__("Incorrect length"));
                $this->setErrorCode(3);
                return false;
            }
            return true;
        }

    }
