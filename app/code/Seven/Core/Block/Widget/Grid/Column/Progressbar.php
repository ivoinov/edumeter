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

	class Core_Block_Widget_Grid_Column_Progressbar extends Core_Block_Widget_Grid_Column_Abstract {
		
		public function __construct($data = array()) {
			if(!isset($data['template']))
				$data['template'] = "widget/grid/progressbar.phtml";
			if(!isset($data['maximal']))
				$data['maximal'] = 100;
			if(!isset($data['minimal']))
				$data['minimal'] = 0;
			parent::__construct($data);
		}
		
		public function getProgressLabel($row, $data) {
			$pattern = parent::getProgressLabel();
			return preg_replace_callback('/\{([a-z_]+)\}/i', function($m) use($data) { if(isset($data[$m[1]])) return $data[$m[1]]; return $m[0]; }, $pattern);
		}
		
		public function getCellValue(Seven_Object $row) {
			$value = parent::getCellValue($row);
			$min = is_numeric($this->getMinimal()) ? $this->getMinimal() : $row->getData($this->getMinimal()); 
			$max = is_numeric($this->getMaximal()) ? $this->getMaximal() : $row->getData($this->getMaximal()); 
			$percentage = ($max - $min) ? round(1000 * ($value - $min) / ($max - $min)) / 10 : 100;
			if($percentage > 100) $percentage = 100;
			if($percentage < 0)   $percentage = 0; 
			$labeltext  = parent::getProgressLabel() ? $this->getProgressLabel($row, array('min' => $min, 'max' => $max, 'percentage' => $percentage, 'value' => $value)) : $value;
			if($this->getReverse())
				$percentage = 100 - $percentage;
			return "<div class=\"widget-column-progressbar\"><div class=\"widget-column-progressbar-filled\" style=\"width:{$percentage}%\"></div><div class=\"widget-column-progressbar-label\">{$labeltext}</div></div>";
		}
		
	}