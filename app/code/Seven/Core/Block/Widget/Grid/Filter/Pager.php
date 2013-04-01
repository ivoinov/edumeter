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

class Core_Block_Widget_Grid_Filter_Pager extends Core_Block_Widget_Grid_Filter_Abstract {
	
	public function apply($collection, $grid) {
		$this->setCollectionCount($this->_getCollectionCount($collection));
		if($this->getPageSize()) {
			$page = $this->getFilterValue();
			$this->setPageCount(ceil($this->getCollectionCount() / $this->getPageSize()));
			if($page > $this->getPageCount() || $page <= 0)
				$page = 1;
			$this->setPageNumber($page);
			$collection->limit($this->getPageSize(), ($page - 1) * $this->getPageSize());
		}
		return $collection;
	}
	
	protected function _getCollectionCount($collection) {
		return $collection->countAll();
	}
	
	public function getPageSize() {
		if($this->getGrid())
			return $this->getGrid()->getPageSize();
		return parent::getPageSize();
	}
	
	public function __construct($data = array()) {
		if(! isset($data['template']))
			$data['template'] = "widgets/grid/pager.phtml";
		if(! isset($data['request_var_name']))
			$data['request_var_name'] = "page";
		parent::__construct($data);
	}

}