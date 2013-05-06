<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 5/6/13
 * Time: 12:15 PM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_Way_Controller_Admin_Stat_Crud extends Core_Controller_Crud_Crud
{

    public function indexAction(){
        $this->getLayout()->addTag("admin_school_way_stat_index");
        parent::indexAction();
    }

    public function updateStatAction()
    {
        
    }
}