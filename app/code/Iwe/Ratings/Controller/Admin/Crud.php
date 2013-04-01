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
        if( !file_exists($filesPath) || !is_readable($filesPath))
            throw  new Exception($filesPath . ' dose\'t exist or dose\'t readable');
        foreach(glob($filesPath . '2012' . DS . '*.txt') as $file)
        {
            if( !file_exists($file) || !is_readable($file))
                throw  new Exception($file . ' dose\'t exist or dose\'t readable');
            $file = BP . DS . 'ENG.txt';
            if($handle = fopen($file,'r+'))
            {
                $cont = 0;
                while(!feof($handle))
                {
                    $line = fgets($handle,1024);
                    $data = explode(';',$line);
                    echo "<hr/>";
                    var_dump(hexdec($line));
//                    var_dump(hexdec($data[3]));
//                    var_dump(hexdec($data[4]));
//                    var_dump(hexdec($data[5]));
//                    var_dump(hexdec($data[6]));
//                    var_dump(hexdec($data[7]));
//                    var_dump(hexdec($data[8]));
//                    var_dump(hexdec($data[9]));
//                    var_dump(hexdec($data[10]));
//                    var_dump(hexdec($data[11]));
//                    var_dump(hexdec($data[12]));
//                    var_dump(hexdec($data[13]));
                    $cont++;
                    if($cont == 100)
                        break;
                }
            }
            //fclose($handle);
            die();
        }
    }


}