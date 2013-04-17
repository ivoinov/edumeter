<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 3/17/13
 * Time: 12:58 AM
 * To change this template use File | Settings | File Templates.
 */
class Iwe_School_Controller_Map extends Core_Controller_Crud_Abstract_List
{
    protected function _getDefaultOptions() {
        return array_merge(parent::_getDefaultOptions(), array(
            'grid_init' => 'iwe_school/entity',
            'collection_alias' => 'iwe_school/entity',
        ));
    }

    public function indexAction()
    {
        $this->getLayout()->addTag('school_index');
        $this->_listAbstract();
    }

    public function getschoolAction()
    {
        $result = array();
        $collection = Seven::getCollection('iwe_school/entity');
        foreach($collection as $school)
        {
            $rate = rand(1 ,10);
            if($rate >= 1 && $rate <= 5)
                $icon = 'red.png';
            elseif($rate >= 6 && $rate <= 7)
                $icon = 'blue.png';
            else
                $icon = 'green.png';
            $icon = $this->_getSkinUrl('images/'.'pin_'.$icon);
            $result[] = array(
                'longitude'  => $school->getLongitude(),
                'latitude'   => $school->getLatitude(),
                'rate'       => $rate,
                'icon'       => $icon,
                'title'      => $school->getName(),
                'city'       => $school->getCity(),
                'id'         => $school->getId()
            );
        }
        Seven::app()->getResponse()
            ->setIsAjax(true)
            ->setBody(json_encode($result));
                return;

    }

    protected function _getSkinUrl($path)
    {
        $area = $this->getLayout()->getArea();
        $theme = $this->getLayout()->getTheme();
        $url = false;
        foreach (array_reverse(Seven::getConfig('design/skin', $area)) as $assert_path) {
            foreach (array($theme, 'default') as $_theme) {
                $url = "/" . $assert_path . "/" . $_theme . "/" . $path;
                if (is_file(BP . str_replace('/', DS, $url))) {
                    return Seven::app()->getRequest()->getBaseUrl() . $url;
                }
            }
        }
        return Seven::app()->getRequest()->getBaseUrl() . $url;

    }
}
