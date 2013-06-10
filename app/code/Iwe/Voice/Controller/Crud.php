<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ilya Voinov
 * User email: ilya.voinov@yahoo.com
 * Date: 6/9/13  
 */
class Iwe_Voice_Controller_Crud extends Core_Controller_Crud_Create
{
    public function addAction()
    {
        $this->getLayout()->addTag('user_voice_add');
        $this->_createAbstract(array(
            'edit_message' => __("Сообщение об ошибки было доставлено. Спасибо."),
            'edit_form_type' => 'iwe_voice/entity',
            'create_redirect' => seven_url('*/school/index'),
            'create_message'=> false
        ));
    }
}