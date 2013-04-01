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
 
class Core_Controller_Crud_Abstract_Delete extends Core_Controller_Crud_Abstract_Create {
	
	protected function _getDefaultOptions() {
		return array_merge(parent::_getDefaultOptions(), array(
			'delete_redirect'		=> $this->getDeleteRedirect() ?: seven_url('*/*/index'),
			'delete_message'		=> __("Record was successfully deleted"),
			'delete_load_key'		=> null,
			'delete_load_id'		=> $this->getDeleteLoadId() 		 ?: $this->getRequest()->getParam($this->getEntityIdParamName() ?: 'id'),
			'list_handlers' 	 	=> $this->getListHandlers() ?: array('abstract_list', 'abstract_list_editable', 'abstract_list_creatable', 'abstract_list_deletable'),		
		));
	}
	
	protected function _prepareDelete($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$options->setLoadKey($options->getDeleteLoadKey());
		$entity = $this->_getEntity($options->getDeleteLoadId(), $options);
		if(!$entity->isLoaded())
			throw new Core_Exception_Noroute('Entity not found');
		if(!$entity->canDelete())
			throw new Core_Exception_Denied('User have no delete permissons');
		$options->setDeleteEntity($entity);
		return $entity;
	}
	
	protected function _processDelete($options) {
		$options = $this->_extendControllerOptions($options);
		$entity = $options->getDeleteEntity();
		try {
			$entity->remove();
			if($options->getDeleteMessage())
				Seven::getSingleton('core/session')->addSuccess($options->getDeleteMessage());
			if($options->getDeleteRedirect())
				$this->redirect($options->getDeleteRedirect());
		} catch(Core_Exception_Invalid $e) {
			Seven::getSingleton('core/session')->addError($e->getMessage());
		}
	}
	
	protected function _deleteAbstract($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->_prepareDelete($options);
		$this->_processDelete($options);
	}
		
}