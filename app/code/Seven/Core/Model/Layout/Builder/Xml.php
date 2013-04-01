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

class Core_Model_Layout_Builder_Xml extends Core_Model_Layout_Builder_Abstract {

	protected $_deleted_marker = array();
	protected $_delayed_handlers = array();
	protected $_delayed_instruction = array();
	protected $_xml_handlers = array();
	protected $_delayed_apply = array();
	protected $_mapping_data = array("template", "wrapper", "before", "after");
	
	public function __construct($layout) {
		$this->_layout = $layout;
	}

    /**
     * @param $name
     */

    protected function _removeBlock($name) {
		$this->_deleted_marker[$name] = $name;		
	}

    /**
     * @param $name
     */

    protected function _restoreBlock($name) {
		unset($this->_deleted_marker[$name]);
	}

    /**
     * @param $package
     * @param $collection
     */

    protected function _collectPackageLayoutFiles(Core_Model_Layout $layout, $package, $collection) {
		$theme = $layout->getTheme();
		foreach(Seven::getConfig('design/paths', $layout->getArea()) as $path) {
			foreach(array('default', $theme) as $_theme) {
				$layout_dir = str_replace('/', DS, $package->getBasePath() . DS . trim($path, '/') . DS . $_theme . DS . "layouts");
				$this->_collectLayoutFilesRecursively($collection, $layout_dir, null, $package->getPool() . DS . $package->getSystemName());
			}
		}		
	}

    /**
     * @param $collection
     */

    protected function _collectDesignLayoutFiles(Core_Model_Layout $layout, $collection) {
		$theme = $layout->getTheme();
		foreach(Seven::getConfig('design/paths', $layout->getArea()) as $path) {
			foreach(array('default', $theme) as $_theme) {
				$this->_collectLayoutFilesRecursively($collection, str_replace('/', DS, BP . DS . "app" . DS . trim($path, '/') . DS . $_theme . DS . "layouts"));
			}
		}		
	}

    /**
     * @param $collection
     * @param $base_path
     * @param null $search_path
     * @param string $layout_id_prefix
     */

    protected function _collectLayoutFilesRecursively($collection, $base_path, $search_path = null, $layout_id_prefix = "") {
		$base_path 	 = rtrim($base_path, DS);
		$search_path = ($search_path === null) ? $base_path : rtrim($search_path, DS);
		
		foreach(glob($search_path . DS . "*", GLOB_ONLYDIR) as $subpath)
			$this->_collectLayoutFilesRecursively($collection, $base_path, $subpath, $layout_id_prefix);

		foreach(glob($search_path . DS . "*.xml") as $layout_file) {
			$layout_id = trim(rtrim($layout_id_prefix, DS) . DS . str_replace($base_path . DS, '', $layout_file), DS);
			$collection[$layout_id] = $layout_file;
		}
	}

    /**
     * @param Core_Model_Layout $layout
     */

    public function load(Core_Model_Layout $layout) {
		$area = $layout->getArea();
		$theme = $layout->getTheme();
		debug()->open("Layout load", array('theme' => $theme, 'area' => $area, 'tags' => array_values($layout->getTags())));

		$collection = new Seven_Collection();
		
		$packages = Seven::app()->getLoadedPackages();
		foreach($packages as $package) {
			$this->_collectPackageLayoutFiles($layout, $package, $collection);
		}
		$this->_collectDesignLayoutFiles($layout, $collection);

		foreach($collection as $path)
			$this->_loadFile($layout, $path);

		foreach($this->_deleted_marker as $name) {
			if($layout->getBlock($name)) {
                $layout->removeBlock($name);
			}
		}
		debug()->close();
	}

    /**
     * @param Core_Model_Layout $layout
     * @param $filename
     */

    protected function _loadFile(Core_Model_Layout $layout, $filename) {
		if(! file_exists($filename))
			return;

		debug()->open('Load layout xml ' . basename($filename), $filename);
		$xml = simplexml_load_file($filename);
		if($packid = (string) $xml->attributes()->package) {
			if(strpos($packid, 'Seven_') === 0)
				$packid = substr($packid, 6);
			$package = Seven::getModel('core/package')->load($packid);

			if(!$package->getAvailabel() || !$package->getActive()) 
				return;
		}
		$_handlers = $layout->getTags();
		$this->_xml_handlers = $_handlers;
		foreach($xml as $index => $node) {
			//echo "&nbsp; Handler: " . $index . "<br>";
			if(isset($_handlers[$index])) {
				$this->_generateLayout($layout, $node, $layout->getBlock("root"));
			} else if(1) { // strtolower ((string)($node->attributes()->allow_apply)) == "true") {
				if(isset($this->_delayed_apply[$index])) {
					foreach($this->_delayed_apply[$index] as $key => $value)
						$this->_generateLayout($layout, $node, $value);
				}
				if(! isset($this->_delayed_handlers[$index]))
					$this->_delayed_handlers[$index] = array();
				$this->_delayed_handlers[$index][] = $node;
			}
		}
		debug()->close();
	}

    /**
     * @param Core_Model_Layout $layout
     * @param SimpleXMLElement $xml
     * @param Core_Block_Abstract $context
     */

    protected function _generateLayout(Core_Model_Layout $layout, SimpleXMLElement $xml, Core_Block_Abstract $context) {
        /** @var $node SimpleXMLElement */
		foreach($xml as $name => $node) {
			//echo "&nbsp; &nbsp; Process instruction {$name}<br>";
			switch($name) {
				case "reference":
					$block_name = (string) ($node->attributes()->name);
					if($block = $layout->getBlock($block_name)) {
						$this->_generateLayout($layout, $node, $block);
					} else {
						if(! isset($this->_delayed_instruction[$block_name]))
							$this->_delayed_instruction[$block_name] = array();
						$this->_delayed_instruction[$block_name][] = $node;
					}
					break;
				case "remove":
					$block_name = (string) ($node->attributes()->name);
					$this->_removeBlock($block_name);
					break;
				case "block":
					$block_name = (string) ($node->attributes()->name);
					$instance = Seven::getBlock((string) ($node->attributes()->type));
					foreach($this->_mapping_data as $attribute) {
						if(isset($node->attributes()->$attribute))
							$instance->setData($attribute, (string) ($node->attributes()->$attribute));
					}
                    $layout->addBlock($block_name, $instance, $context);
					$this->_generateLayout($layout, $node, $instance);
					if(isset($this->_delayed_instruction[$block_name])) {
						foreach($this->_delayed_instruction[$block_name] as $node) {
							$this->_generateLayout($layout, $node, $instance);
						}
					}
					break;
				case "restore":
					$block_name = (string) ($node->attributes()->name);
					$this->_restoreBlock($block_name);
					break;
				case "action":
					$method = (string) ($node->attributes()->method);
					$param = $this->_xml2array($node);
					call_user_func_array(array($context, $method), $param);
					break;
				case "apply":
					$handler_name = ((string) ($node->attributes()->handler));
					$this->_xml_handlers[$handler_name] = $handler_name;
					if(! empty($this->_delayed_handlers[$handler_name])) {
						foreach($this->_delayed_handlers[$handler_name] as $node) {
							$this->_generateLayout($layout, $node, $context);
						}
					}
					if(isset($this->_delayed_apply[$handler_name]))
						$this->_delayed_apply[$handler_name] = array();
					$this->_delayed_apply[$handler_name][] = $context;
					break;
			}
		}
	
	}

    /**
     * @param SimpleXMLElement $xml_object
     * @param bool $skip_attributes
     * @return array
     */

    protected function _xml2array(SimpleXMLElement $xml_object, $skip_attributes = false) {
		$xml_array = array();
		$to_translate = ($xml_object->attributes()->translate && ! $skip_attributes) ? array_map('trim', explode(',', $xml_object->attributes()->translate)) : array();
		foreach($xml_object as $index => $node)
			$xml_array[$index] = (is_object($node) && $node->count()) ? $this->_xml2array($node, $skip_attributes) : (! in_array($index, $to_translate) ? (string) $node : __((string) $node));
		return $xml_array;
	}

}
