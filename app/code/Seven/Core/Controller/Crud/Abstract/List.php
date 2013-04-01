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
 
abstract class Core_Controller_Crud_Abstract_List extends Core_Controller_Crud_Abstract_View {
	
	protected function _getDefaultOptions() {
		return array_merge(parent::_getDefaultOptions(), array(
			'grid_init'			 => $this->getGridInit()		 ?: $this->getUse(),
			'collection_alias'   => $this->getCollectionAlias()  ?: $this->getUse(),
			'list_registry_name' => $this->getListRegistryName() ?: 'abstract_list',
			'list_handlers' 	 => $this->getListHandlers()     ?: 'abstract_list',
		));
	}
	
	/**
	 * @return Core_Resource_Entity_Collection
	 */
	
	protected function _getCollection($options = array()) {
		$options = $this->_extendControllerOptions($options);
		return Seven::getCollection($options->getCollectionAlias());
	}

	protected function _prepareList($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$collection = $this->_getCollection($options);
		if(!$options->getSkipAccessCheck())
			$collection->filterReadable();
		Seven::register($options->getListRegistryName(), $collection);
		return $collection;
	}
	
	protected function _renderList($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->getLayout()->addTag($options->getListHandlers());
		$this->loadLayout();
		$this->_prepareListBlock($options);
		$this->renderLayout();
	}
	
	protected function _prepareListBlock($options) {
		if($options->getGridInit() && ($block = $this->getLayout()->getBlock('grid'))) {
			$block->initXml($options->getGridInit());
			if($block->getParent()->getTitle() === null)
				$block->getParent()->setTitle($block->getTitle());
		}
	}
	
	protected function _listAbstract($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->_prepareList($options);
		$this->_renderList($options);
	}
	
}
