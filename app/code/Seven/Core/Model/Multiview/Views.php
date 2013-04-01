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

class Core_Model_Multiview_Views {

	public function getFrontendSites() {
		$views = array();
		foreach(Seven::getCollection('core/website')->filter('area', 'frontend')->load() as $website) {
			$views[$website->_getId()] = $website->getName();
		}
		return $views;
	}
	
	public function getI18nViews() {
		$views = array();
		foreach(Seven::getCollection('core/locale')->load() as $locale) {
			$views[$locale->_getId()] = $locale->getName();
		}
		return $views;
	}
	
}