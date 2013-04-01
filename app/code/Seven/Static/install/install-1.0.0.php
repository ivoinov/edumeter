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
 * @package    Static
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

$this->start();

$this->query("CREATE TABLE `static_block` (
  `id` varchar(255) NOT NULL,
  `_view_id` varchar(5) NOT NULL DEFAULT '0',
  `title` varchar(255) NULL,
  `content` text,
  PRIMARY KEY (`id`,`_view_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8");

$this->end();