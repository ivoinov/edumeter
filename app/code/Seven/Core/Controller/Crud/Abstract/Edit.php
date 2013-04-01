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
 
class Core_Controller_Crud_Abstract_Edit extends Core_Controller_Crud_Abstract_List {
	
	protected function _getDefaultOptions() {
		return array_merge(parent::_getDefaultOptions(), array(
			'edit_registry_name' 	 => $this->getEditRegistryName() ?: 'abstract_edit',
			'edit_handlers' 		 => $this->getEditHandlers()     ?: array('abstract_edit', 'abstract_crud_form'),
			'edit_multiview_handler' => 'abstract_edit_multiview',
			'edit_load_key'			 => null,
			'edit_load_id'			 => $this->getEditLoadId() 		 ?: $this->getRequest()->getParam($this->getEntityIdParamName() ?: 'id'),
			'edit_view'				 => $this->getEditView() 		 ?: $this->getRequest()->getParam($this->getEditViewParamName() ?: 'view_id'),
			'edit_form_type'		 => $this->getEditFormType()     ?: 'core/form_entity',
			'edit_form_init' 		 => $this->getEditFormInit() 	 ?: $this->getUse(),
			'edit_redirect'			 => $this->getEditRedirect()	 ?: seven_url('*/*/index'),
			'edit_message'			 => __("Record was successfully updated"),
			'list_handlers' 		 => $this->getListHandlers()     ?: array('abstract_list', 'abstract_list_editable'),		
		));
	}
	

	protected function _getForm($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$form = Seven::getModel($options->getFormType());
		if($options->getFormInit()) {
			foreach((array) $options->getFormInit() as $init)
				$form->initXml($init);
		}
		return $form;
	}
	
	protected function _prepareEdit($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$options->setLoadKey($options->getEditLoadKey());
		$options->setEntityView($options->getEditView());
		$entity = $this->_getEntity($options->getEditLoadId(), $options);
		if(!$entity->isLoaded())
			throw new Core_Exception_Noroute('Entity not found');
		if(!$options->getSkipAccessCheck() && !$entity->canRead())
			throw new Core_Exception_Denied('User have no read permissons');
		if(!$options->getSkipAccessCheck() && !$entity->canUpdate())
			throw new Core_Exception_Denied('User have no update permissons');
		$options->setEditEntity($entity);
		$options->setFormType($options->getEditFormType());
		$options->setFormInit($options->getEditFormInit());
		$options->setEditForm($form = $this->_getForm($options));
		if($entity instanceof Core_Model_Multiview_Entity) {
			if($options->getEditMultiviewHandler()) {
				$options->setEditHandlers(array_merge($options->getEditHandlers(), (array) $options->getEditMultiviewHandler()));
			}
		}
		Seven::register($options->getEditRegistryName(), $form);
		Seven::register($options->getEditRegistryName() . "_entity", $entity);
		return $entity;
	}
	
	protected function _processEdit($options) {
		$options = $this->_extendControllerOptions($options);
		$entity = $options->getEditEntity();
		$form   = $options->getEditForm();		
		$this->_loadFormEdit($options);				
		if($form->isSubmit()) {
			try {
				if($form->isValid()) {
					$this->_saveFormEdit($options);
				} else {
					throw new Core_Exception_Invalid(__('Some of the form fields are filled with invalid values'));
				}
			} catch(Core_Exception_Invalid $e) {
				Seven::getSingleton('core/session')->addError($e->getMessage());
			}
		}
	}
	
	protected function _loadFormEdit($options) {
		$options = $this->_extendControllerOptions($options);
		$entity = $options->getEditEntity();
		$form   = $options->getEditForm();
		$form->load($entity);
	}
	
	protected function _saveFormEdit($options) {
		$options = $this->_extendControllerOptions($options);
		$entity = $options->getEditEntity();
		$form   = $options->getEditForm();
		if(!$options->getSkipAccessCheck() && !$entity->canUpdate())
			throw new Core_Exception_Denied('User have no update permissons');
		$form->save();
		if($options->getEditMessage())
			Seven::getSingleton('core/session')->addSuccess($options->getEditMessage());
		if($options->getEditRedirect())
			$this->redirect($options->getEditRedirect());
	}
	
	protected function _renderEdit($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->getLayout()->addTag($options->getEditHandlers());
		$this->render();
	}
	
	protected function _editAbstract($options = array()) {
		$options = $this->_extendControllerOptions($options);
		$this->_prepareEdit($options);
		$this->_processEdit($options);
		$this->_renderEdit($options);
	}
		
}