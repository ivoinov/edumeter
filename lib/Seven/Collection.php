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
 * @package    Libs
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

    class Seven_Collection extends ArrayObject {

        public function add($model){
            $this->append($model);
        }
        
        public function first(){
            if ($this->count() == 0)
                return NULL;
            return $this->offsetGet(0);
        } 

        public function last() {
            if ($this->count() == 0)
                return NULL;            
            return $this->offsetGet($this->count() - 1);
        }

    }