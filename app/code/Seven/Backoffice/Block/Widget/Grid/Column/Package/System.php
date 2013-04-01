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
 * @package    Backoffice
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

class Backoffice_Block_Widget_Grid_Column_Package_System extends Core_Block_Widget_Grid_Column_Select {
	
	protected function _apply($collection, $grid) {
		if($this->getFilterValue() !== null && $this->getFilterValue() !== '') {
			$condition = $this->getFilterValue() ? 'in' : 'nin';
			$sys_packages = (array)Seven::getConfig('required_packages');
			foreach($sys_packages as $id => $state) {
				if(strpos($id, 'Seven_') === 0)
					$sys_packages[] = substr($id, 6);
			}
			$collection->filter('id', array($condition => $sys_packages));
		}
	}	

	public function getOptions() {
		return array(1 => __('Yes'), 0 => __('No'));
	}
	
	protected function _getRawCellValue(Seven_Object $row) {
		return Seven::getHelper('backoffice/package')->isSystem($row);
	}
	
}
