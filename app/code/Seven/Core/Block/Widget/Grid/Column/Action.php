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

class Core_Block_Widget_Grid_Column_Action extends Core_Block_Widget_Grid_Column_Abstract {
	
	public function getFilterValue() {
		return null;
	}
	
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->addHtmlClass('widget-grid-actions');
	}
	
	protected function _getActionsArray($row) {
		$actions = array();
		$default = array('label' => "", 'icon' => $this->getSkinUrl("images/icons/cog.png"));
		foreach($this->getActions() as $key => $val) {
			$disabled = (isset($val['disabled_callback']) && call_seven_callback($val['disabled_callback'], NULL, $row)) || (isset($val['enabled_callback']) && ! call_seven_callback($val['enabled_callback'], NULL, $row));
			if($disabled && empty($val['show_disabled']))
				continue;
			$val['disabled'] = $disabled;
			if(! isset($val['icon']) && isset($val['skin_icon']))
				$val['icon'] = $this->getSkinUrl($val['skin_icon']);
                        if(!isset($val['html_classes']))
                            $val['html_classes'] = 'grid-action-button';
			$val = array_merge($default, $val);
			if(! isset($val['url_callback']))
				$val['url_callback'] = array($this, 'getActionUrl');
			$href = call_seven_callback($val['url_callback'], NULL, $row, $key);
			$actions[] = $this->_generateActionHtml($href, $val);
		}
		return $actions;
	}
	
	public function getActionUrl($row, $action_name) {
		if(($action = $this->getAction($action_name)) == NULL)
			return;
		if(isset($action['url_template']))
			return seven_url(preg_replace_callback('/\{([a-z_0-9]+)\}/i', function ($m) use($row) {
				return $row->getData($m[1]);
			}, $action['url_template']));
		if(isset($action['url']))
			return seven_url($action['url']);
		return "";
	}
	
	public function getAction($action) {
		$actions = $this->getActions();
		if(isset($actions[$action]))
			return $actions[$action];
		return null;
	}
	
	public function getActions() {
		return (array) parent::getActions();
	}
	
	protected function _generateActionHtml($href, $val) {
		if($this->getDisplayMode() == 'labels') {
			return "<a href=\"{$href}\">" . $val['label'] . "</a>";
		} elseif($this->getDisplayMode() == 'icons') {
			return "<a href=\"{$href}\" title=\"{$val['label']}\"><img src=\"{$val['icon']}\" alt=''></a>";
		} else {
			$disabled = empty($val['disabled']) ? "" : " disabled='disabled'";
			return "<button class = '{$val['html_classes']}'{$disabled}onclick=\"document.location='{$href}';return false;\"><img src='{$val['icon']}' alt=''> <span>{$val['label']}</span></button>";
		}
	}
	
	public function getCellValue(Seven_Object $row) {
		$actions = $this->_getActionsArray($row);
		return implode(" ", $actions);
	}
	
	protected function _getFilterHtml() {
		return "";
	}
	
}