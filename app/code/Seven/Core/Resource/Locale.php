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

class Core_Resource_Locale extends Core_Resource_Entity {

	protected $_alias = "core/locale";

	public function loadDictionary($locale) {
		$paths = $locale->getDictionaryPaths();
		$cache_key = "locale_dict_" . md5(implode(':', $paths) . ":::" . $locale->getLanguage());
		if(!($dictionary = Seven::cache()->load($cache_key))) {
			$dictionary = (array)$locale->getDictionary();
			if($locale->getLanguage()) {
				$reader = new Seven_File_Reader_Mo();
				if($paths) {
					foreach((array)$paths as $path) {
						if($locale->isLoadedDictionaryPath($path))
							continue;
						debug()->open('Load locale from ' . $path);
						$locale->addLoadedDictionaryPath($path);
						$files = array();
						if(is_file($path)) {
							$files = array($path);
						} else {
							if(is_dir($path . DS . $locale->getLanguage()))
								$files = glob($path . DS . $locale->getLanguage() . DS . "*.mo");
							if(is_file($path . DS . $locale->getLanguage() . ".mo"))
								$files[] = $path . DS . $locale->getLanguage() . ".mo";
						}
						foreach($files as $file) {
							$local_dict = array();
							$reader->open($file)->load($local_dict)->close();
							$dictionary = array_merge_recursive_replace($dictionary, $local_dict);
							if(preg_match('/plural=([-\(\)n0-9%><|& =?:!]+)/', $reader->getHeader('plural_forms'), $m)) {
								$dictionary[':plural:'] = $m[1];
							}
						}
						debug()->close();
					}
				}
			}
			Seven::cache()->save($dictionary, $cache_key, array('locale_dict'));
		}
		$locale->setDictionary($dictionary);
		return $locale;
	}

}
