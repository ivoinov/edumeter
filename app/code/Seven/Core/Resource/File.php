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

class Core_Resource_File extends Core_Resource_Entity {
	
	public function init(&$object) {
		
	} 
	
	public function load(&$object, $id, $id_field = NULL) {
		$object->_setSearchValue($id);
		$object->_setSearchField('path');
		
		if(! is_file($id))
			return $object->setData(array());
		
		if(0 && Seven::getConfig('resources/identity_map')) {
			$prim_id = realpath($object->_getPrimaryId());
			$identity_map = Seven::getObjectCache($this->getAlias() . '::identity_map');
			if(array_key_exists($prim_id, (array) $identity_map))
				return $object = $identity_map[$prim_id];
			$identity_map[$prim_id] = $object;
		}
		
		$object->loadData(array('path' => $id, 'size' => filesize($id), 'name' => basename($id), 'dir_name' => dirname($id), 'mime' => function_exists('mime_content_type') ? mime_content_type($id) : 'application/unknown'));
		
		return $object;
	}
	
	public function save(&$object) {
		umask(0);
		$original = new Seven_Object($object->_getOriginalData());
		if(!$object->_getId()) { // generate path automatically
			if(!$object->getName())
				$object->setName(md5(time() . mt_rand(0, 99999) . microtime(true)));
			if(!$object->getDirName())
				$object->setDirName(BP . DS . 'var' . DS . 'tmp');
				$object->setPath(rtrim($object->getDirName(), DS) . DS . $object->getName());
		} else {
			if(!$object->getName())
				$object->setName(basename($object->getPath()));
			if(!$object->getDirName())
				$object->setDirName(dirname($object->getPath()));
			$object->setPath(rtrim($object->getDirName(), DS) . DS . $object->getName());
		}
		if($object->getMkDir() !== false && !is_dir($object->getDirName()))
			mkdir($object->getDirName(), 0777, true);
		if(is_file($original->getPath()) && $object->getPath() && $original->getPath() != $object->getPath())
			rename($original->getPath(), $object->getPath());
		if(is_file($object->getSource())) {
			if($object->getKeepSource() === false)
				rename($object->getSource(), $object->getPath());
			else
				copy($object->getSource(), $object->getPath());
		
		}
		$this->load($object, $object->_getId());
	}
	
	public function remove(&$object) {
		if(is_file($object->getPath()))
			unlink($object->getPath());
	}
	
	public function getKey() {
		return 'path';
	}

}