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

$this->query("ALTER TABLE `iwe_school_stat` DROP `name`;");
$this->query("ALTER TABLE `iwe_school_stat` DROP `subject`;");

$this->query("ALTER TABLE `iwe_school_stat` ADD `school_id` VARCHAR(255) NOT NULL;");
$this->query("ALTER TABLE `iwe_school_stat` ADD `subject_id` VARCHAR(255) NOT NULL;");

$this->query("CREATE TABLE `iwe_ratings_subjects` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255) NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");


$this->end();