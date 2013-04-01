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

class Core_Model_Layout {

    protected $_renderer = null;

    protected $_builder  = null;

    protected $_blocks      = array();
    protected $_root        = null;
    protected $_area        = null;
    protected $_theme       = null;
    protected $_handlers    = array('default' => 'default');

    protected $_tags = array('default' => 'default');

    public function __construct() {
        $this->addTag("default");
        $this->addBlock('root', $this->_root = Seven::getBlock("core/root")->setLayout($this));
    }

    /**
     * @param $mode
     * @return Core_Model_Layout
     */

    public function setRenderer(Core_Model_Layout_Renderer_Abstract $renderer) {
        $this->_renderer = $renderer;
        return $this;
    }

    /**
     * @return Core_Model_Layout_Renderer_Abstract
     */

    public function getRenderer() {
        return $this->_renderer;
    }

    /**
     * @param $mode
     * @return Core_Model_Layout
     */

    public function setBuilder(Core_Model_Layout_Builder_Abstract $builder) {
        $this->_builder = $builder;
        return $this;
    }

    /**
     * @return Core_Model_Layout_Builder_Abstract
     */

    public function getBuilder() {
        return $this->_builder;
    }

    /**
     * @param string $area
     * @return Core_Model_Layout
     */

    public function setArea($area) {
        $this->_area = $area;
        return $this;
    }

    /**
     * @return string
     */

    public function getArea() {
        if(!$this->_area)
            $this->_area = "frontend";
        return $this->_area;
    }

    /**
     * @todo move this out
     * @return Core_Model_Layout
     */

    public function loadTranslations() {
		foreach(Seven::getConfig('design/paths', $this->getArea()) as $path)
			foreach(array('default', $this->getTheme()) as $_theme)
				Seven::getSingleton('core/session')->getLocale()->addDictionaryPath(BP . DS . 'app' . DS . $path . DS . $_theme . DS . 'i18n');
		return $this;
	}
	
    /**
     * @return null|string
     */

    public function getTheme() {
		if(!$this->_theme)
			$this->_theme = "default";
		return $this->_theme;
	}

    /**
     * @param $theme
     * @return Core_Model_Layout
     */

    public function setTheme($theme) {
        $this->_theme = $theme;
        return $this;
    }

    /**
     * @return Core_Model_Layout
     */

    protected function _prepare() {
		debug()->open("Layout prepare");
		$this->_root->prepare();
		debug()->close();
		return $this;
	}

    /**
     * @return mixed
     * @throws Core_Exception_Layout
     */

    protected function _render() {
        debug()->open("Layout render");
        if(!$this->getRenderer())
            throw new Core_Exception_Layout("No renderer specified");

        $result = $this->getRenderer()->render($this);
        debug()->close();
        return $result;
    }

    /**
     * @return string
     */

    public function render() {
        $this->_prepare();
		return $this->_render();
	}


    public function load() {
        debug()->open("Build layout");
        if(!$this->getBuilder())
            throw new Core_Exception_Layout("Builder not specified");
        $this->getBuilder()->load($this);
        debug()->close();
    }

    /**
     * @return Core_Block_Root
     */

    public function getRootBlock() {
        return $this->_root;
    }

    /**
     * @return array
     */

    public function getBlocks() {
		return $this->_blocks;
	}

    /**
     * @param $name
     * @param Core_Block_Abstract $instance
     * @param Core_Block_Abstract $parent
     * @return Core_Model_Layout
     */

    public function addBlock($name, Core_Block_Abstract $instance, Core_Block_Abstract $parent = NULL) {
		$instance->setLayoutName($name);
        $instance->setLayout($this);
		$this->_blocks[$name] = $instance;
		if($parent)
			$parent->addChild($name, $instance);
		return $this;
	}

    /**
     * @param $name
     * @return Core_Model_Layout
     */

    public function removeBlock($name) {
		if(isset($this->_blocks[$name]) && $this->_blocks[$name]->getParent())
			$this->_blocks[$name]->getParent()->removeChild($name);
		unset($this->_blocks[$name]);
		return $this;
	}
	
	/**
	 * Return an block instance
	 * @param string $name
	 * @return Core_Block_Abstract
	 */
	
	public function getBlock($name) {
		if(isset($this->_blocks[$name]))
			return $this->_blocks[$name];
		return NULL;
	}
	/**
	 * Add Layout tag to Page layout
	 * @param string|array $tags
	 */
	
	public function addTag($tags) {
		if($tags) {
			foreach((array)$tags as $tagline) {
				foreach(explode(' ', $tagline) as $tag) {
					$tag = trim($tag); 
					$this->_tags[$tag] = $tag;
				}
			}
		}
		return $this;
	}
	
	/**
	 * Add layout tag
	 * @param string|array $name
	 * @deprecated use addTag instead
	 */
	
	public function addHandler($name) {
		return $this->addTag($name);
	}
	
	/**
	 * Return an array of Layout tags
	 * @deprecated use getTags instead
	 * @return array
	 */
	
	public function getHandlers() {
		return $this->getTags();
	}
	
	/**
	 * Return an array of Layout tags
	 * @return array
	 */
	
	public function getTags() {
		return $this->_tags;
	}
	
	/**
	 * Remove tag
	 * @param $tag
	 */
	
	public function removeTag($tag) {
		unset($this->_tags[$tag]);
		return $this;
	}

    /**
     * @deprecated use removeTag instead
     * @param $tag
     * @return Core_Model_Layout
     */

    public function removeHandler($tag) {
		return $this->removeTag($tag);
	}

    /**
     * Remove all tags
     * @deprecated use setTags with empty array instead
     */

    public function resetHandler() {
		return $this->setTags(array());
	}

    /**
     * @param $tags
     */

    public function setTags($tags) {
		$this->_tags = array();
		$this->addTag($tags);
        return $this;
	}
	
}