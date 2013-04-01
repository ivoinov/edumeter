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

$this->query("ALTER TABLE `pages_entity` CHANGE `title` `title` VARCHAR(255) NULL");
$this->query("ALTER TABLE `pages_entity` CHANGE `content` `content` TEXT NULL");
$this->query("ALTER TABLE `pages_entity` CHANGE `_view_id` `_view_id` INT NOT NULL");

$this->end();