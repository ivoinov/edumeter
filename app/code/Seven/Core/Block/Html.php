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

    class Core_Block_Html extends Core_Block_Abstract {
         
        static protected $_doctypes = array(
            'html4-strict'        => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">',
            'html4-transitional'  => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""http://www.w3.org/TR/html4/loose.dtd">',
            'html4-frameset'      => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
            'html5'          => '<!DOCTYPE HTML>'
        );

        protected function _toHtml() {
            return $this->_getDocType() . "\n<html>\n" . $this->getChildrenHtml() . "</html>\n";
        }
     /**
     *@todo add Doctype to html doc from xml file
     *@return string
     */       
        
        protected function _getDocType(){
            $type = $this->getDocType();
            if(empty($type)) return "";
            return isset(self::$_doctypes[$type]) ? self::$_doctypes[$type] : "<?DOCTYPE {$type}>";
        }  
         
     }