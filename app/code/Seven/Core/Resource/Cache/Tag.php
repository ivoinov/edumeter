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

	class Core_Resource_Cache_Tag extends Core_Resource_Entity {
		
		protected $_alias = "core/cache_tag";
		protected $_key   = "tag";
		
		public function load(&$object, $id, $id_field = NULL) {
			if(!empty($id)) {
				$object->loadData(Seven::getConfig('cache/tags/' . $id, 'global'))->setTag($id);
			}
		}
		
		public function save(&$object) {
			throw new Exception('Save method is not applicable to cache tag');
		}
		
		protected function _getInitData() {
			return array('name' => 'Cache storage', 'tag' => 'global', 'status' => 0, 'last_update' => '0000-00-00 00:00:00');
		}
		
	}