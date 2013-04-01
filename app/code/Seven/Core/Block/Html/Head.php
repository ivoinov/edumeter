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

class Core_Block_Html_Head extends Core_Block_Template{
    
    protected $_items = array();
    
    public function __construct($data = array()) {
        if(empty($data['template'])) 
            $data['template'] = 'page/head.phtml';
        parent::__construct($data);
    }
    
    public function addCss($location, $type = 'skin') {
        $this->addItem($location, $type, 'css');
        return $this;
    }
    
    public function addJs($location, $type = 'skin') {
        $this->addItem($location, $type, 'js');
        return $this;
    }
    
    public function addMetaHttp($name, $content){
        if(!isset($this->_items['metahttp'])) $this->_items['metahttp'] = array();
        $this->_items['metahttp'][$name]= $content;
        return $this;
    }

    public function addMeta($name, $content){
        if(!isset($this->_items['meta'])) $this->_items['meta'] = array();
        $this->_items['meta'][$name]= $content;
        return $this;
    }

    /**
     *  @todo add item to items array what contain <head> tag element 
     *  @param text $location,$location_type,$type
     *  @return array()
     */
    public function addItem($location, $location_type, $type) {
        if(!isset($this->_items[$type])) $this->_items[$type] = array();
        $this->_items[$type][$this->_getFileLocation($location, $location_type)] = $this->_getFileLocation($location, $location_type);
        return $this;
    }
    
    public function removeItem($location,$location_type,$type) {
        if(!isset($this->_items[$type])) 
            throw Core_Exception_Error('Undefined type '.$type);
        if(isset($this->_items[$type][$this->_getFileLocation($location, $location_type)]))
            unset($this->_items[$type][$this->_getFileLocation($location, $location_type)]);
        return $this;        
    }
    
    public function removeCss($location, $type = 'skin') {
        $this->removeItem($location, $type, 'css');
        return $this;
    }
    
    public function removeJs($location, $type = 'skin') {
        $this->removeItem($location, $type, 'js');
        return $this;
    }
    protected function _getItem($type){
        if(empty($this->_items[$type]))
                return array();
        return $this->_items[$type];
    }
    
    public function setFavicon($icon) {
        $location = $this->getSkinUrl($icon);       
        return parent::setFavicon($location);
    }

    public function getFavicon() {
        if($favicon = parent::getFavicon())
            return $favicon;
        // load fav icon form site config
        try {
            $favicon = Seven::getModel('core/image')->load(Seven::getSiteConfig("general/design/favicon"));
            if($favicon->_getId())
                return $favicon->getUrl();
        } catch(Exception $e) {

        }
        return "";
    }

    public function hasFavicon() {
        return $this->getFavicon() ? true : false;
    }

    public function getTitle() {
        if($title = parent::getTitle())
            return $title;
        return Seven::getSiteConfig("general/site/name");
    }

    public function prepare() {
        if(Seven::isDeveloperMode()) {
            $this->addJs('seven/debug/debug.js',  'lib');
            $this->addCss('seven/debug/debug.css', 'lib');
        }
     	return parent::prepare();
    }
    
    public function addTitle($name){
        $this->_items['title'] = $name;
        return $this;
    }
    
    protected function _toHtml(){
        return "<head>\n" . parent::_toHtml() . $this->getChildrenHtml() . "</head>\n";
    }
    
    protected function _getFileLocation($location,$location_type) {
        if(!preg_match('/^https?:\/\//', $location)) {
            if($location_type == 'skin') {
                    $location = $this->getSkinUrl($location);
            } else if($location_type == 'lib') {
                $location = (string)Seven::app()->getRequest()->getBaseUrl() . "/public/jslib/" . $location;
            }
        }        
        return $location;
    }
}