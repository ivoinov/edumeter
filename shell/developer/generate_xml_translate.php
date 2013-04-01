#!/usr/bin/php
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
 * @package    Libs
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class XML_Translate_Generator {

	protected $_of = null;

	public function start($path, $phpoutputfile) {
		if($this->_of = fopen($phpoutputfile, "w")) {
			$this->put("<?php");
			$this->_process($path);
			fclose($this->_of);
		}
	}

	protected function _process($path) {
		if(!$path || $path[strlen($path) - 1] != DIRECTORY_SEPARATOR) $path = $path . DIRECTORY_SEPARATOR;
		if(is_dir($path)) {
			if ($dh = opendir($path)) {
				while(($file = readdir($dh)) !== false) {
					if($file == "." || $file == "..") continue;
					if(is_dir($path.$file))
					$this->_process($path.$file);
				}
				closedir($dh);
			}
			$files = glob($path . "*.xml");
			foreach($files as $file) {
				echo $file . "\n";
				$this->put("// ". $file);
				$this->_processXMLFile($file);
			}
		}
	}

	protected function _processXMLFile($file) {
		return $this->_processXML(simplexml_load_file($file));
	}

	protected function _processXML($xml) {
		if(!$xml instanceof SimpleXMLElement) return;
		$attr = (array)$xml->attributes();
		$translate = array();
		if(!empty($attr['@attributes']['translate']))
		$translate = array_map('trim', explode(",", $attr['@attributes']['translate']));
		foreach($xml->children() as $tag => $child) {
			if(in_array($tag, $translate)) {
				$str = (string)$child;
				if(!empty($str))
				$this->put("__(\"".addslashes($str)."\");");
			}
			$this->_processXML($child);
		}
	}

	protected function put($text) {
		return fwrite($this->_of, $text . "\n");
	}

}

$base_path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . "app";
foreach(glob($base_path . DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR . "*") as $pool_path) {
	if(!is_dir($pool_path)) continue;
	foreach(glob($pool_path . DIRECTORY_SEPARATOR . "*") as $package_path) {
		if(!is_dir($package_path)) continue;
		if(!file_exists($package_path . DIRECTORY_SEPARATOR . "i18n")) continue;
		$xml_generate = new XML_Translate_Generator();
		$xml_generate->start($package_path, $package_path . DIRECTORY_SEPARATOR . "i18n" . DIRECTORY_SEPARATOR . "xml-translate.php");
	}
}
foreach(glob($base_path . DIRECTORY_SEPARATOR . "design" . DIRECTORY_SEPARATOR . "*") as $area_path) {
	foreach(glob($area_path . DIRECTORY_SEPARATOR . "*") as $theme_path) {
		if(!is_dir($theme_path)) continue;
		if(!file_exists($theme_path . DIRECTORY_SEPARATOR . "i18n")) continue;
		$xml_generate = new XML_Translate_Generator();
		$xml_generate->start($theme_path, $theme_path . DIRECTORY_SEPARATOR . "i18n" . DIRECTORY_SEPARATOR . "xml-translate.php");
	}
}
