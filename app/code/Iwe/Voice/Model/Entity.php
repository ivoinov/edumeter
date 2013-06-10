<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ilya Voinov
 * User email: ilya.voinov@yahoo.com
 * Date: 6/9/13  
 */
class Iwe_Voice_Model_Entity extends Core_Model_Entity
{
    public function getVoiceType()
    {
        return array(
            1 =>    'Type 1',
            2 =>    'Type 2',
            3 =>    'Type 3',
        );
    }
}