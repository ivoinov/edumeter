<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ilya Voinov
 * User email: ilya.voinov@yahoo.com
 * Date: 5/16/13  
 */
$this->start();

$this->query("ALTER TABLE  `iwe_school_entity` CHANGE  `search_str`  `search_str` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");


$this->end();