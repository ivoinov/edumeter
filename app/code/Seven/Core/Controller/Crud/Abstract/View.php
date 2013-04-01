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
 
abstract class Core_Controller_Crud_Abstract_View extends Core_Controller_Abstract {
	
	protected function _getDefaultOptions() {
		return array(
			'entity_alias'       => $this->getEntityAlias()      ?: $this->getUse(),
			'view_registry_name' => $this->getViewRegistryName() ?: 'abstract_view',
			'view_handlers' 	 => $this->getViewHandlers()     ?: 'abstract_view',
			'view_load_key'		 => null,
			'view_load_id'		 => $this->getViewLoadId() 		 ?: $this->getRequest()->getParam($this->getEntityIdParamName() ?: 'id'),
			'skip_access_check'  => $this->getSkipAccessCheck()
		);
	}
	
	/**
	 * @return Seven_Object
	 */
	
	protected function _extendControllerOptions($options) {
		if(!($options instanceof Seven_Object)) 
			$options = new Seven_Object((array)$options);
		if(!$options->getData('__extended')) {
			$options->setData(array_merge($this->_getDefaultOptions() ,$options->getData()));
			$options->setData('__extended', true);
		} 
		return $options;
	}
	
	/**
	 * @return Core_Model_Entity
	 */
	
	protected function _getEntity($id = null, $options = array()) {
		$options = $this->_extendControllerOptions($options);
		$entity = Seven::getModel($options->getEntityAlias());
		if($id !== null) {
			if($entity instanceof Core_Model_Multiview_Entity)
				$entity->_setView($options->getEntityView());
			$entity->load($id, $options->getLoadKey());			
		}
		return $entity;
	}

	protected function _prepareView($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$options->setLoadKey($options->getViewLoadKey());
		$entity = $this->_getEntity($options->getViewLoadId(), $options);
		if(!$entity->isLoaded())
			throw new Core_Exception_Noroute('Entity not found');
		if(!$options->getSkipAccessCheck() && !$entity->canRead())
			throw new Core_Exception_Denied('User have no read permissons');
		Seven::register($options->getViewRegistryName(), $entity);
		return $entity;
	}
	
	protected function _renderView($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->getLayout()->addTag($options->getViewHandlers());
		$this->render();
	}
	
	protected function _viewAbstract($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->_prepareView($options);
		$this->_renderView($options);
	}
	
}
