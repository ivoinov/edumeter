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
 * @package    Seven
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

final class Seven {
	
	static function isDeveloperMode() {
		return ! empty($_SERVER['SEVEN_DEVELOPER_MODE']);
	}
	
	/**
	 * Get Application version
	 *
	 * @param bool $asString return version as string
	 */
	
	static function version($asString = true) {
		return self::app()->version($asString);
	}
	
	/**
	 * Application model
	 *
	 * @var Core_Model_Application
	 */
	
	protected static $_app_model = null;
	
	/**
	 * Cache model
	 *
	 * @var Core_Model_Cache
	 */
	
	protected static $_cache_model = null;
	
	/**
	 * Get Application cache model
	 * @todo implement Seven_Cache cover for Zend_Cache
	 * @return Zend_Cache_Core
	 */
	
	static function cache() {
		if(empty(self::$_cache_model)) {
			$backend = Seven::getConfig('resources/cache/backend',	 'system');
			if(!empty($backend['cache_dir']) && $backend['cache_dir']{0} === DS)
				$backend['cache_dir'] = BP . DS . $backend['cache_dir'];
			if(isset($backend['cache_dir']) && !is_dir($backend['cache_dir']))
				mkdir($backend['cache_dir'], 0777, true);
			$frontend = Seven::getConfig('resources/cache/frontend', 'system');
			if(empty($frontend['cache_id_prefix']))
				$frontend['cache_id_prefix'] = md5(BP);
			self::$_cache_model = Zend_Cache::factory(
				'Core', 
				Seven::getConfig('resources/cache/type', 'system'), 
				$frontend, 
				$backend
			);
		}
		return self::$_cache_model;
	}
	
	/**
	 * Get Application Model instance
	 *
	 * @return Core_Model_Application
	 *
	 */
	
	static function app() {
		if(self::$_app_model === null)
			self::$_app_model = new Core_Model_Application();
		return self::$_app_model;
	}
	
	static protected function _renderErrorPage($e, $error) {
		while(ob_get_level() > 0) {
			ob_end_clean();
		}
		$enviroment = self::isDeveloperMode() ? "developement" : "production";
		require_once "errors/" . $enviroment . "/" . $error . ".php";
	}
	
	/**
	 * Run Application
	 *
	 */
	
	static function run() {
		try {
			self::app()->run();
		} catch(Exception $e) {
			self::log(E_ERROR, $e);
			self::_renderErrorPage($e, 500);
		}
		return true;
	}
	
	/**
	 * Get XML configuration node
	 *
	 * @param string $option path to option
	 */
	
	static function getConfig($option, $area = null) {
		return self::app()->getConfig($option, $area);
	}
	
	/**
	 * Get site configuration
	 *
	 * @param string $option path to option
	 */
	
	static function getSiteConfig($option) {
		return self::app()->getSiteConfig($option);
	}
	
	/**
	 * Get Database adapter
	 *
	 * @param string $type adapter type
	 * @return Seven_Db_Adapter_Pdo_Mysql
	 */
	
	static function getDatabaseAdapter($type = 'default') {
		$connections = self::getObjectCache('database_adapters');
		if(empty($connections[$type])) {
			$config = new Seven_Object(Seven::getConfig('resources/db/' . $type, 'system'));
			$connections[$type] = Seven_Db::factory($config->getType(), $config);
		}
		return $connections[$type];
	}
	
	/**
	 * Get Model by class alias
	 *
	 * @param string $alias
	 * @param mixed $data
	 * @return Core_Model_Abstract
	 */
	
	static function getModel($alias, $data = NULL) {
		return self::getObjectByAlias($alias, 'model', $data);
	}
	
	/**
	 * Get Resource (DAO) by class alias
	 *
	 * @param string $alias
	 * @param mixed $data
	 * @return Core_Resource_Abstract
	 */
	
	static function getResource($alias, $data = NULL) {
		$cache = self::getObjectCache('resource');
		$class_name = self::getClassByAlias($alias, 'resource');
		if(! isset($cache[$class_name])) {
			/*if(!class_exists($class_name) && self::getConfig("entity/" . $alias)) {
				$base_class = self::getConfig("entity/" . $alias . "/base_class");
				if(empty($base_class))
					$base_class = "core/entity";
				real_class_alias(self::getClassByAlias($base_class, 'resource'), $class_name);
			}*/
			$cache[$class_name] = new $class_name();
		}
		return $cache[$class_name];
	}
	
	static function getCollection($alias, $data = NULL) {
		$class_name = self::getClassByAlias($alias . "_collection", 'resource');
		/*if(! class_exists($class_name) && self::getConfig("entity/" . $alias)) {
			$base_class = self::getConfig("entity/" . $alias . "/base_class");
			if(empty($base_class))
				$base_class = "core/entity";
			real_class_alias(self::getClassByAlias($base_class . '_collection', 'resource'), $class_name);
		}*/
		return new $class_name();
	}
	
	/**
	 * Get Helper by class alias
	 *
	 * @param string $alias
	 * @param mixed $data
	 */
	
	static function getHelper($alias, $data = NULL) {
		$cache = self::getObjectCache('helper');
		$class_name = self::getClassByAlias($alias, 'helper');
		if(!isset($cache[$class_name]))
			$cache[$class_name] = new $class_name();
		return $cache[$class_name];
	}
	
	/**
	 * Get Block by class alias
	 *
	 * @param string $alias
	 * @param mixed $data
	 * @return Core_Block_Abstract
	 */
	
	static function getBlock($alias, $data = NULL) {
		return self::getObjectByAlias($alias, 'block', $data);
	}
	
	/**
	 * Replaced classes
	 */
	
	protected static $_replacedClasses = array();
	
	/**
	 * Get Classname by class alias
	 *
	 * @param string $alias alias
	 * @param string $type  object type
	 */
	
	static function getClassByAlias($alias, $type) {
		if(strpos($alias, "/") !== false) {
			list($scope, $class_name) = explode("/", $alias);
			$class_name = implode('_', array_map('ucfirst', explode('_', $scope))) . "_" . ucfirst($type) . "_" . implode("_", array_map("ucfirst", explode("_", $class_name)));
		} else {
			$class_name = $alias;
		}
		
		if(isset(self::$_replacedClasses[$class_name]))
			return self::$_replacedClasses[$class_name];
		
		$replaced_with = Seven::getConfig('class_map/' . $class_name, 'global');
		if($replaced_with && count($replaced_with)) {
			$original_name = $class_name; 
			foreach($replaced_with as $replaced_class_name)	{
				class_alias($class_name, $replaced_class_name . "_ParentClass");
				$class_name = $replaced_class_name;
			}
			self::$_replacedClasses[$original_name] = $class_name;
			return $class_name; 
		}
	
		return $class_name;
	}
	
	/**
	 * Reset replaced classes
	 */
	
	static function resetReplacedClasses() {
		self::$_replacedClasses = array();
	}
	
	/**
	 * Get class alias by Classname  
	 *
	 * @param string $class_name Classname
	 * @param string $type object type
	 * @return string $alias
	 */
	static function getAliasByClass($class_name, $type) {
		$type = "_" . $type . "_";
		$alias = implode("/", explode($type, strtolower($class_name), 2));
		return $alias;
	}
	
	/**
	 * Get Object by class alias
	 *
	 * @param string $alias alias
	 * @param string $type  object type
	 * @param mixed $data
	 */
	
	static function getObjectByAlias($alias, $type, $data = NULL) {
		$class_name = self::getClassByAlias($alias, $type);
		if(!$class_name)
			throw new Exception("Class name is empty for alias '{$alias}'");
		$object = new $class_name($data);
		return $object;
	}
	
	/**
	 * Get Model singleton by class alias
	 *
	 * @param string $alias
	 * @param mixed $data
	 * @return Core_Model_Abstract
	 */
	
	static function getSingleton($alias) {
		$singleton = self::getObjectCache('singleton');
		$class_name = self::getClassByAlias($alias, 'model');
		if(empty($singleton[$class_name])) {
			$singleton[$class_name] = self::getModel($alias);
		}
		return $singleton[$class_name];
	}
	
	/**
	 * Object cache
	 */
	
	protected static $_objectCache = array();
	
	/**
	 * Return pointer to object cache array
	 */
	
	static function getObjectCache($type) {
		if(! isset(self::$_objectCache[$type]))
			self::$_objectCache[$type] = new ArrayObject();
		return self::$_objectCache[$type];
	}
	
	/**
	 *
	 *
	 */
	
	static function resetObjectCache($type = null) {
		if($type === null) {
			self::$_objectCache = array();
			self::$_app_model = null;
			self::$_cache_model = null;
		} else
			self::$_objectCache[$type] = new ArrayObject();
	}
	
	/**
	 * Registry hash array
	 *
	 * @var array
	 */
	
	protected static $_registry = array();
	
	/**
	 * Add variable to registry
	 *
	 * @param string 	$var 	name
	 * @param mixed 	$val
	 * @param bool 		$force	force set
	 * @throws Exception
	 */
	
	static function register($var, $val, $force = false) {
		if(isset(self::$_registry[$var]) && !$force)
		   throw new Exception("Registry '{$var}' already defined");
		self::$_registry[$var] = $val;
	}
	
	/**
	 * Return registry value
	 *
	 * @param mixed $var
	 */
	
	static function registry($var) {
		if(! isset(self::$_registry[$var]))
			return NULL;
		return self::$_registry[$var];
	}
	
	/**
	 * Check if registry setup
	 *
	 * @param string $var name
	 */
	
	static function registered($var) {
		return isset(self::$_registry[$var]);
	}
	
	/**
	 * Dispatch event (run observers)
	 *
	 * @param string 	$key	event key
	 * @param mixed 	$args	event args
	 */
	
	static function event($key, $args = array()) {
		return self::app()->event($key, $args);
	}

	/**
	 * Application system log
	 *
	 * @param int		$level		Message level (E_WARNING, E_ERROR, E_NOTICE, etc)
	 * @param string 	$message	Message
	 * @param string	$category	Message category
	 */
	
	static function log($level, $message, $category = null) {
		if($category === null)
			$category = (PHP_SAPI !== 'cli') ? "system" : "cli";
		$rotator = gmdate("Ymd");
		if(!is_dir(BP . DS . "var" . DS . "log" . DS . $rotator))
			mkdir(BP . DS . "var" . DS . "log" . DS . $rotator, 0777, true);
		if($file = fopen(BP . DS . "var" . DS . "log" . DS . $rotator . DS . $category . ".log", "a")) {
			$level_label = function ($level) {
				if($level == E_ERROR)
					return "Error";
				if($level == E_WARNING)
					return "Warning";
				if($level == E_NOTICE)
					return "Notice";
				return "Unknown message";
			};
			fprintf($file, "[%s] %s: %s\n", date("H:i:s"), $level_label($level), $message);
			debug()->log($level_label($level), $message);
			fclose($file);
			return true;
		}
		return false;
	}
	
}
