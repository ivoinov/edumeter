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

class Core_Block_Html_Body extends Core_Block_Template{
    protected $_items = array();
    
    public function __construct($data = array()) {
        if(empty($data['template'])) 
            $data['template'] = 'page/body.phtml';
        parent::__construct($data);
    }  
    
    public function toHtml(){
        return "<body>\n" . parent::toHtml() . "</body>\n";
    }
}