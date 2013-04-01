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

$this->query("CREATE TABLE IF NOT EXISTS `pages_entity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `_view_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`,`_view_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8");

$this->end();