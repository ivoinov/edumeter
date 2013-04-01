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
 * @package    Backoffice
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Backoffice_Controller_System_Config extends Core_Controller_Abstract {

    public function indexAction() {        
        $scope = Seven::app()->getRequest()->getParam('scope', 'global');
        $scopes = call_seven_callback('core/website_config::getScopesArray', 'model');
        if(!in_array($scope, array_keys($scopes)))
        	throw new Core_Exception_Noroute('Scope not exists');
        
        $form = Seven::getModel('backoffice/form_system_config')
                ->setScope($scope)
                ->load();
        
        if($form->isSubmit()) {
            if($form->isValid()) {
                Seven::getSingleton('core/session')->addSuccess(__('Configuration has been updated'));
                $form->save();
                $this->redirect(Seven::app()->getRequest());
            } else {
                Seven::getSingleton('core/session')->addError(__("Can't save new configuration"));
            }
        }

        $layout = $this->getLayout()->addTag('system_configuration_page');
        $this->loadLayout();
        if($block = $layout->getBlock("system_config")) {
        	$block->addButton('scope', array(
                    'class'     => 'core/widget_form_input_select',
                    'html_name' => 'scope',
                    'options'   => $scopes,
                    'value'     => $scope,
                    'label'     => __("Choose website"),
                    'order'     => -1000,
                    'top_only'  => true,
                ))
                ->setModel($form);
        }
        $layout->render();
    }
}
