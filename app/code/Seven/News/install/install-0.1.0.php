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
 * @package    News
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

$this->start();

$this->query("CREATE TABLE IF NOT EXISTS `news_entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `author` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");

$this->query("CREATE TABLE IF NOT EXISTS `news_entity_view` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `_view_id` varchar(5) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `anounse` text,
  `content` text,
  PRIMARY KEY (`id`,`_view_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;");

$this->end();