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
 
class Core_Controller_Crud_Abstract_Create extends Core_Controller_Crud_Abstract_Edit {
	
	protected function _getDefaultOptions() {
		return array_merge(parent::_getDefaultOptions(), array(
			'create_registry_name' 	=> $this->getCreateRegistryName() ?: 'abstract_create',
			'create_handlers' 	 	=> $this->getCreateHandlers() ?: array('abstract_create', 'abstract_crud_form'),
			'create_form_type'	 	=> $this->getEditFormType() ?: $this->getCreateFormType() ?: 'core/form_entity',
			'create_form_init' 	 	=> $this->getEditFormInit() ?: $this->getCreateFormInit() ?: $this->getUse(),
			'create_redirect'		=> $this->getCreateRedirect() ?: seven_url('*/*/index'),
			'create_message'		=> __("Record was successfully added"),
			'list_handlers' 	 	=> $this->getListHandlers() ?: array('abstract_list', 'abstract_list_editable', 'abstract_list_creatable'),		
		));
	}
	
	protected function _prepareCreate($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$entity = $this->_getEntity(null, $options);
		if(!$options->getSkipAccessCheck() && !$entity->canCreate())
			throw new Core_Exception_Denied('User have no create permissons');
		$options->setCreateEntity($entity);
		$options->setFormType($options->getCreateFormType());
		$options->setFormInit($options->getCreateFormInit());
		$options->setCreateForm($form = $this->_getForm($options));
		Seven::register($options->getCreateRegistryName(), $form);
		Seven::register($options->getCreateRegistryName() . "_entity", $entity);
		return $entity;
	}
	
	protected function _processCreate($options) {
		$options = $this->_extendControllerOptions($options);
		$entity = $options->getCreateEntity();
		$form   = $options->getCreateForm();		
		$this->_loadFormCreate($options);				
		if($form->isSubmit()) {
			try {
				if($form->isValid()) {
					$this->_saveFormCreate($options);
				} else {
					throw new Core_Exception_Invalid(__('Some of the form fields are filled with invalid values'));
				}
			} catch(Core_Exception_Invalid $e) {
				Seven::getSingleton('core/session')->addError($e->getMessage());
			}
		}
	}
	
	protected function _loadFormCreate($options) {
		$options = $this->_extendControllerOptions($options);
		$entity = $options->getCreateEntity();
		$form   = $options->getCreateForm();
		$form->load($entity);
	}
	
	protected function _saveFormCreate($options) {
		$options = $this->_extendControllerOptions($options);
		$entity = $options->getCreateEntity();
		$form   = $options->getCreateForm();
		if(!$options->getSkipAccessCheck() && !$entity->canCreate())
			throw new Core_Exception_Denied('User have no update permissons');
		$form->save();
		if($options->getCreateMessage())
			Seven::getSingleton('core/session')->addSuccess($options->getCreateMessage());
		if($options->getCreateRedirect())
			$this->redirect($options->getCreateRedirect());
	}
	
	protected function _renderCreate($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->getLayout()->addTag($options->getCreateHandlers());
		$this->render();
	}
	
	protected function _createAbstract($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->_prepareCreate($options);
		$this->_processCreate($options);
		$this->_renderCreate($options);
	}
		
}