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
 * @method Core_Model_Request getRequest()
 */

abstract class Core_Controller_Abstract extends Seven_Object {

    /** @var Core_Model_Layout */
    protected $_layout = null;

    /**
     * @param $route
     * @return bool
     */

    public function isActionExists($route) {
		return method_exists($this, $this->_getActionName($route));
	}

    /**
     * @param $action
     * @return mixed
     * @throws Core_Exception_Denied
     * @throws Core_Exception_Noroute
     */

    public function dispatch($action) {
		if($this->isActionExists($action)) {
			if(!$this->isAllowed($action))
				throw new Core_Exception_Denied('Access to this action denied');
			return call_user_func(array($this, $this->_getActionName($action)));
		}

        throw new Core_Exception_Noroute('Action not exists ' .  $action);
	}

    /**
     * @param $action
     * @return bool
     */

    public function isAllowed($action) {
		$permissions = new Seven_Object(array('allowed' => null, 'route' => $this->getRequest(), 'action' => $action));
		Seven::app()->event('route_access', $permissions);
		return $permissions->getAllowed() || $permissions->getAllowed() === null;
	}

    /**
     * @param $action
     * @return string
     */

    protected function _getActionName($action) {
		return preg_replace_callback("/_([a-z])/", function ($matches) {
			return strtoupper($matches[1]);
		}, $action) . "Action";
	}

    /**
     * @param Core_Model_Layout $layout
     * @return Core_Controller_Abstract
     */

    public function setLayout(Core_Model_Layout $layout) {
        $this->_layout = $layout;
        return $this;
    }

    /**
     * @return Core_Model_Layout_Builder_Xml
     */

    protected function getLayoutBuilder() {
        return Seven::getSingleton('core/layout_builder_xml');
    }

    /**
     * @return Core_Model_Layout_Renderer_Http
     */

    protected function getLayoutRenderer() {
        /** @var $renderer Core_Model_Layout_Renderer_Http */
        $renderer = $this->getRequest()->isAjax() ? Seven::getSingleton("core/layout_renderer_ajax") :  Seven::getSingleton("core/layout_renderer_http");
        $renderer->setResponse(Seven::app()->getResponse());
        return $renderer;
    }

	/**
	 * @return Core_Model_Layout
	 */

	public function getLayout() {
        if($this->_layout === null) {
		    $this->_layout = Seven::getModel("core/layout")
                                ->setArea(Seven::app()->getWebsite()->getArea())
                                ->setTheme(trim(Seven::getSiteConfig("general/design/theme")))
                                ->setBuilder($this->getLayoutBuilder())
                                ->setRenderer($this->getLayoutRenderer());
        }
        return $this->_layout;
	}

    /**
     * @return string
     */

    public function render() {
		return $this->renderLayout();
	}

    /**
     * @return string
     */

    public function renderLayout() {
		$this->loadLayout();
		return $this->getLayout()->render();
	}

    /**
     * @var bool
     */

    protected $_load_layout = false;

    /**
     *
     */

    protected function _prepareLayoutTags() {
		if(Seven::getConfig('layout/use_url_tags')) {
			$controller_id = str_replace('/', '_', Seven::app()->getRequest()->getRouteNode()->getId());
			$action_id     = str_replace('/', '_', Seven::app()->getRequest()->getAction());
			$this->getLayout()->addTag($controller_id . "_ALL");
			$this->getLayout()->addTag($controller_id . "_" . $action_id);
		}

        /** @var $node Core_Model_Router_Node */
        $node = Seven::app()->getRequest()->getRouteNode();
		
		if($node && $node->getConfig('layout_tags'))
			$this->getLayout()->addTag($node->getConfig('layout_tags'));
		if($node->getActionConfig('layout_tags'))
			$this->getLayout()->addTag($node->getActionConfig('layout_tags'));
	}

    /**
     * @return Core_Controller_Abstract
     */

    public function loadLayout() {
		if(!$this->_load_layout) {
            $this->getLayoutXml();
			$this->_prepareLayoutTags();
			$this->getLayout()->load();
		}
		$this->_load_layout = true;
		return $this;
	}

	public function redirect($location, $code = 302) {
		return Seven::app()->getResponse()->redirect($location, $code);
	}

	public function forward($location) {
		Seven::app()->forward($location);
	}

	public function redirectReferer($args = array(), $code = 302) {
		$referer = Seven::app()->getRequest()->getHeader('referer');
		return $this->redirect(_url($referer, $args), $code);
	}
}