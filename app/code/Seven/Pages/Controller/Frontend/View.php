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

class Pages_Controller_Frontend_View extends Core_Controller_Crud_Abstract_View {

	public function getUse() {
		return 'pages/page';
	}
	
	public function indexAction() {
		$this->_viewAbstract(array(
			'view_load_id'  => preg_replace('/\.html$/', '', $this->getRequest()->getParam('page')),
			'view_load_key' => 'title',
			'entity_view'   => Seven::app()->getWebsite()->_getId(),
		));
	}
	
}
