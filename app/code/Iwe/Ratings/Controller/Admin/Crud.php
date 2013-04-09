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
        $filesPath = BP . DS . 'var' . DS . 'stat'. DS . '2010';

        $stat = Seven::getModel('iwe_ratings/rating');
        $school = Seven::getModel('iwe_school/school');
        foreach( glob($filesPath . DS . '*.txt') as $file )
        {
            if($handle = fopen($file,'r+'))
            {
                while(!feof($handle))
                {
                    $line = fgets($handle,1024);
                    $data = explode(';',$line);
                    $schoolInfo = $this->_getSchoolInfo($data);
                    $schoolName = $data[2];
                    $passedNumber = $this->_getPassedCount($data[13]);
                }
                fclose($handle);
            }
        }
    }

    protected function _getSchoolInfo($data)
    {
        $info = array();
        for($i = 3; $i <= 12; $i++)
        {
            $info[] = round( $this->_getAcsiiHex( str_split($data[$i]) ), 2 );
        }
        return $info;
    }

    protected function _getAcsiiHex($symbols)
    {
        if(isset($symbols[0]))
            unset($symbols[0]);
        $number = '';
        foreach($symbols as $i => $symbol)
        {
            if( $i == 3 )
                $number .= '.' . dechex(ord($symbol));
            else
                $number .= dechex(ord($symbol));
        }
        return floatval($number);
    }

    protected function _getPassedCount($char)
    {
        $number = '';
        $symbols = str_split($char);
        foreach($symbols as $i => $symbol)
        {
            return floatval(ord($symbol));
        }

    }
}