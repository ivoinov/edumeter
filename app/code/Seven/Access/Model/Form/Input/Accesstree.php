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
 * @package    Access
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */
	
	class Access_Model_Form_Input_Accesstree extends Core_Model_Form_Input_Abstract {
		
		public function getRoot() {
			$by_areas = array();
			foreach(Seven::getConfig('areas') as $key => $area) {
				$by_areas[$key] = $area;
				$by_areas[$key]['routes'] = array_merge_recursive_replace((array)Seven::getConfig('routing/routes', 'global'), (array)Seven::getConfig('routing/routes', $key));
			}
			return $by_areas;
		}
		
	}