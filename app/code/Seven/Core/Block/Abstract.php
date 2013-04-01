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

/**
 * @method Core_Block_Abstract setLayout(Core_Model_Layout $layout)
 */

abstract class Core_Block_Abstract extends Seven_Object {

    protected $_children = array();
    protected $_parent = NULL;
    protected $_name = NULL;
    protected $_sort_children = array();
    static $incremental_id = 0;

    public function __construct($data = array()) {
        parent::__construct($data);
        $this->getHtmlId();
    }

    /**
     * @return Core_Block_Abstract
     */

    public function prepare() {
        foreach ($this->getChildren() as $child)
            $child->prepare();
        return $this;
    }

    /**
     * @return Core_Model_Layout
     */

    public function getLayout() {
        if (!($layout = parent::getLayout())) {
            if (!($parent = $this->getParent()) || !($layout = $parent->getLayout()))
                throw new Core_Exception_Layout("Layout not specified for block '" . $this->getLayoutName() . "'");
        }
        return $layout;
    }

    /**
     * @return string
     */

    public function getHtmlId() {
        $html_id = parent::getHtmlId();
        if (!$html_id) {
            $html_id = "id" . Core_Block_Abstract::$incremental_id++;
            parent::setHtmlId($html_id);
        }
        return $html_id;
    }

    /**
     * @return Core_Block_Abstract|null
     */

    public function getParent() {
        return $this->_parent;
    }

    /**
     * @return null
     */

    public function getLayoutName() {
        return $this->_name;
    }

    /**
     * @param Core_Block_Abstract $parent
     * @return Core_Block_Abstract
     */

    public function setParent(Core_Block_Abstract $parent) {
        $this->_parent = $parent;
        return $this;
    }

    /**
     * @return null
     */

    public function getParentName() {
        return $this->getParent() ? $this->getParent()->getName() : NULL;
    }

    /**
     * @param $name
     * @return Core_Block_Abstract
     */

    public function setLayoutName($name) {
        $this->_name = $name;
        return $this;
    }

    /**
     * @return array
     */

    public function getChildren() {
        return $this->_children;
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */

    public function addChild($name, $value) {
        $value->setParent($this);
        $value->setLayout($this->getLayout());
        $value->setOrderWeight(count($this->_children) * 100);
        return $this->_children[$name] = $value;
    }

    /**
     * @param $name
     * @param null $newname
     * @return Core_Block_Abstract
     */

    public function takeChild($name, $newname = null) {
        if ($child = $this->getLayout()->getBlock($name)) {
            if ($parent = $child->getParent())
                $parent->removeChild($name);
            $this->addChild($newname ? : $name, $child);
        }
        return $this;
    }

    /**
     * @param $name
     */

    public function removeChild($name) {
        unset($this->_children[$name]);
    }

    /**
     * @return mixed
     */

    public function toHtml() {
        debug()->open('Block <u>' . ($this->getLayoutName() ? $this->getLayoutName() : get_class($this)) . '</u> rendering', array(
            'class' => get_class($this),
            'template' => $this->getTemplate(),
            'name' => $this->getName()
        ));
        $html = $this->_toHtml();
        if (($this->_isDebugMode() && $this->_isBodyBlock()) || $this->getWrapper()) {
            $wrapper = Seven::getBlock('core/wrapper');
            if ($this->_isDebugMode() && $this->_isBodyBlock())
                $wrapper->setHtmlClasses(array('system-block', strtolower(str_replace('_', '-', get_class($this)))))
                    ->setTitle($this->getLayoutName() . " (" . get_class($this) . ")")
                    ->setHtmlId($this->getLayoutName());
            foreach (explode(" ", $this->getWrapper()) as $wrap) {
                if (!($wrap = trim($wrap))) continue;
                if (preg_match('/^#/', $wrap))
                    $wrapper->setHtmlId(substr($wrap, 1));
                else if (preg_match('/^\./', $wrap))
                    $wrapper->addHtmlClass(substr($wrap, 1));
                else
                    $wrapper->setHtmlTag($wrap);
            }
            $html = $wrapper->wrapHtml($html);
        }
        debug()->close();
        return $html;
    }

    /**
     * @return null
     */

    final public function toAjax() {
        return $this->_toAjax();
    }

    /**
     * @return null
     */

    protected function _toAjax() {
        return NULL;
    }

    /**
     * @return bool
     */

    protected function _isDebugMode() {
        return Seven::getConfig('debug/layout') && Seven::isDeveloperMode();
    }

    /**
     * @return bool
     */

    protected function _isBodyBlock() {
        return !in_array($this->getName(), array('root', 'head', 'html', 'body'));
    }

    /**
     * @return mixed
     */

    abstract protected function _toHtml();

    /**
     * @param $child_name
     * @return string
     */

    public function getChildHtml($child_name) {
        $child = $this->getChild($child_name);
        if ($child)
            return $child->toHtml();
        return "";
    }

    /**
     * @param $child_name
     * @return Core_Block_Abstract
     */

    public function getChild($child_name) {
        if (isset($this->_children[$child_name]))
            return $this->_children[$child_name];
        return NULL;
    }

    /**
     * @return string
     */

    public function getChildrenHtml() {
        $html = "";
        foreach ($this->_children as $child)
            $html .= $child->toHtml();
        return $html;
    }

    /**
     * @deprecated since 0.5.0, use seven_url instead
     */

    public function getUrl($url, $args = array()) {
        return seven_url($url, $args);
    }

    /**
     * @param array $args
     * @return Core_Model_Request
     */

    public function getCurrentUrl($args = array()) {
        $url = clone Seven::app()->getRequest();
        $url->setParameters(array_merge_recursive_replace($url->getParameters(), $args));
        return $url;
    }

    /**
     * @param $path
     * @return string
     */

    public function getSkinUrl($path) {
        $area = $this->getLayout()->getArea();
        $theme = $this->getLayout()->getTheme();
        $url = false;
        foreach (array_reverse(Seven::getConfig('design/skin', $area)) as $assert_path) {
            foreach (array($theme, 'default') as $_theme) {
                $url = "/" . $assert_path . "/" . $_theme . "/" . $path;
                if (is_file(BP . str_replace('/', DS, $url))) {
                    return Seven::app()->getRequest()->getBaseUrl() . $url;
                }
            }
        }
        return Seven::app()->getRequest()->getBaseUrl() . $url;
    }

    /**
     * @param $url
     * @return string
     */

    public function getMediaUrl($url) {
        return Seven::app()->getRequest()->getBaseUrl() . $url;
    }

    /**
     * @param null $attributes
     * @return string
     */

    public function getAttributeString($attributes = NULL) {
        if ($attributes === NULL)
            $attributes = (array)$this->getHtmlAttributes();
        $attributes_raw = array();
        foreach ($attributes as $key => $value) {
            if ($value === false || $value === null || $value === "")
                $attributes_raw[] = "$key";
            else
                $attributes_raw[] = "$key=\"" . htmlspecialchars($value) . "\"";

        }
        return implode(" ", $attributes_raw);
    }

    /**
     * @param $new_classes
     * @return Core_Block_Abstract
     */

    public function addHtmlClass($new_classes) {
        if (is_string($new_classes))
            $new_classes = explode(' ', $new_classes);
        $classes = $this->getHtmlClasses();
        foreach ($new_classes as $class) {
            if ($class = trim($class))
                $classes[$class] = $class;
        }
        $this->setHtmlClasses($classes);
        return $this;
    }

    /**
     * @param $class
     * @return Core_Block_Abstract
     */

    public function removeHtmlClass($class) {
        $classes = $this->getHtmlClasses();
        unset($classes[$class]);
        $this->setHtmlClasses($classes);
        return $this;
    }

    /**
     * @return array
     */

    public function getHtmlClasses() {
        $classes = parent::getHtmlClasses();
        if (is_string($classes)) {
            $raw = explode(' ', $classes);
            $classes = array();
            foreach ($raw as $class) {
                if ($class = trim($class))
                    $classes[$class] = $class;
            }
            parent::setHtmlClasses($classes);
        }
        return $classes ? (array)$classes : array();
    }

    /**
     * @return mixed
     */

    public function getHtmlAttributes() {
        $attributes = parent::getHtmlAttributes();
        if ($classes = implode(' ', (array)$this->getHtmlClasses()))
            $attributes['class'] = $classes;
        $attributes['id'] = $this->getHtmlId();
        if ($this->getWidth())
            $attributes['width'] = $this->getWidth();
        if ($this->getHeight())
            $attributes['height'] = $this->getHeight();
        $events = array('onchange', 'onclick', 'onmouseover', 'onmouseout', 'onmousemove', 'onsubmit');
        foreach ($events as $event)
            if ($value = $this->getData($event))
                $attributes[$event] = $value;
        return $attributes;
    }

    /**
     * @return array
     */

    public function getSortedChildren() {
        if (!$this->_sort_children)
            $this->_sortChildren();
        return $this->_sort_children;
    }

    /**
     *
     */

    protected function _sortChildren() {
        $this->_sort_children = $this->_children;
        foreach ($this->_sort_children as $child) {
            $after = $child->getAfter() ? $this->getChild($child->getAfter()) : NULL;
            $before = $child->getBefore() ? $this->getChild($child->getBefore()) : NULL;
            if ($after && $before) {
                $child->setOrderWeight(($after->getOrderWeight() + $before->getOrderWeight()) / 2);
            } else if ($after) {
                $child->setOrderWeight($after->getOrderWeight() + 1);
            } else if ($before) {
                $child->setOrderWeight($before->getOrderWeight() - 1);
            }
        }
        usort($this->_sort_children, function ($a, $b) {
            if ($a->getOrderWeight() > $b->getOrderWeight()) return 1;
            if ($a->getOrderWeight() == $b->getOrderWeight()) return 0;
            return -1;
        });
    }

    /**
     * @return string
     */

    public function getSortedChildrenHtml() {
        $html = "";
        foreach ($this->getSortedChildren() as $child)
            $html .= $child->toHtml();
        return $html;
    }

}