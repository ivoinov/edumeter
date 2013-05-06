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

$this->query("CREATE TABLE `iwe_stat_way` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `way` INT(11) NOT NULL,
      `school` INT(11) NOT NULL,
      `year` VARCHAR(255) NOT NULL,
      `rate` INT(11) NOT NULL,
      PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");

$this->end();