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
 * @deprecated since 1.0.0 use Core_Controller_Abstract_Crud
 */

class Core_Controller_Deprecated_Crud_Abstract extends Core_Controller_Deprecated_List_Abstract {
	
	public function getEntityAlias() {
		if($alias = parent::getEntityAlias())
			return $alias;
		return $this->getAlias();
	}
	
	public function getFormAlias() {
		if($alias = parent::getFormAlias())
			return $alias;
		return $this->getAlias();
	}
	
	public function getFormModel() {
		return Seven::getModel('core/form_entity')->initXml($this->getFormAlias());
	}
	
	/**
	 * 	Render / Process Form for entity add and edit
	 */ 
	
	protected function _renderForm() {
		$this->getLayout()->addTag('default_crud_index');
				
		$entity = Seven::getModel($this->getEntityAlias());
		
		if($entity instanceof Core_Model_Multiview_Entity)
			$entity->_setView(Seven::app()->getRequest()->getParam('view_id'));
		
		if(Seven::app()->getRequest()->getParam('id') !== null) {
			$entity = $entity->load(Seven::app()->getRequest()->getParam('id')); // load by ID
			if($entity->_getId() === null) {
				throw new Core_Exception_Noroute('Entity not found');
			}
		}

		$this->_beforeFormLoad($entity);
		$form = $this->getFormModel()->load($entity);
		$this->_afterFormLoad($entity, $form);
		
		if($form->isSubmit()) {
			try {
				if($form->isValid()) {
					$this->_beforeSave($entity, $form);
					$form->save();
					$this->_afterSave($entity, $form);
				} else {
					throw new Core_Exception_Invalid(__('Some of the form fields are filled with invalid values'));
				}
			} catch(Core_Exception_Invalid $e) {
				Seven::getSingleton('core/session')->addError($e->getMessage());
			}
		}
		
		// render form
		$this->getLayout()->addTag($entity->_getId() ? 'default_crud_edit' : 'default_crud_add');
		$this->getLayout()->addTag('default_crud_form');
		if($entity instanceof Core_Model_Multiview_Entity)
			$this->getLayout()->addTag('default_crud_multiview_form');
		
		$this->loadLayout();
		if($block = $this->getLayout()->getBlock('form')) {
			$block->setModel($form);
			if($entity instanceof Core_Model_Multiview_Entity)
				$this->_prepareMultiviewFormBlock($block, $entity);
		
		}
		if($block = $this->getLayout()->getBlock('view_swicher')) {
			$block->setOptions($entity->_getViews());
		}
		$this->_beforeFormRender($entity, $form);
		$this->renderLayout();
	}
	
	protected function _prepareMultiviewFormBlock($block, $entity) {
		if(Seven::app()->getRequest()->getAction() == 'add') return;
		
		$block->setButtonsTop(true)->addButton('view_id', array('class' => 'core/widget_form_input_select', 'html_name' => 'view_id', 'options' => $entity->_getViews(), 'value' => $entity->_getView(), 'label' => __("Choose a view"), 'order' => - 1000, 'top_only' => true));
		foreach($block->getButtons() as $id => $button)
			if($button->getBottomOnly() === null)
				$button->setBottomOnly($id != 'view_id');
	}
		
	/**
	 *	Delete entity action prototype 
	 */ 
	
	protected function _deleteAction() {
		$entity = Seven::getModel($this->getEntityAlias())->load(Seven::app()->getRequest()->getParam('id'))->remove(); // load by ID
		$this->_afterDelete();
	}
	
	/**
	 * 	Code placeholders 
	 */ 

	protected function _beforeFormLoad(Seven_Object $entity) {
	}
	
	protected function _afterFormLoad(Seven_Object $entity, Core_Model_Form $form) {
	}
	
	protected function _beforeFormRender(Seven_Object $entity, Core_Model_Form $form) {
	}

	protected function _beforeSave(Seven_Object $entity, Core_Model_Form $form) {
	}
	
	protected function _afterSave(Seven_Object $entity, Core_Model_Form $form) {
		Seven::getSingleton('core/session')->addSuccess($entity->_getId() ? __("Record was successfully updated") : __("Record was successfully added"));
		$this->redirect(seven_url('*/*/index'));
	}
	
	protected function _afterDelete() {
		$this->redirect(seven_url('*/*/index'));
	}

	protected function _prepareList() {
		$this->getLayout()->addTag('default_crud_index');
		return parent::_prepareList();
	}
	
}

