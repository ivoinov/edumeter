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
 *
 */

    class Core_Resource_Image_Collection extends Core_Resource_Entity_Collection {

        public function getSelect() {
            if($this->_select === NULL)
                $this->_select = Seven::getDatabaseAdapter()
                    ->select($this->getTable())
                    ->joini(Seven::getResource("core/file")->getTable() . ":file", array($this->getTable() . '.file_id = file.file_id'));
            return $this->_select;        
        }

    }