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
 * @package    Pages
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

$this->start();

$this->query("ALTER TABLE  `iwe_school_entity` ADD  `description` TEXT NULL DEFAULT NULL");
$this->query("ALTER TABLE  `iwe_school_entity` CHANGE  `phone`  `phone` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
$this->query("ALTER TABLE  `iwe_school_entity` ADD  `longitude` FLOAT( 10 ) NULL DEFAULT NULL ,ADD  `latitude` FLOAT( 10 ) NULL DEFAULT NULL");

$this->end();