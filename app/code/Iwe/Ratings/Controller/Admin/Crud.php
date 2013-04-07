<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 3/28/13
 * Time: 11:16 PM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_Ratings_Controller_Admin_Crud extends Core_Controller_Crud
{
    protected function _getDefaultOptions() {
        return array_merge(parent::_getDefaultOptions(), array(
            'list_handlers' => $this->getListHandlers() ?: array('abstract_list', 'abstract_list_editable', 'abstract_list_creatable', 'abstract_list_deletable','admin_rate_index'),
        ));
    }

    public function processStatAction()
    {
        $filesPath = BP . DS . 'var' . DS . 'stat'. DS ;

        $stat = Seven::getModel('iwe_ratings/rating');
        $school = Seven::getModel('iwe_school/school');
    }


}