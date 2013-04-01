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

class Core_Model_Session extends Core_Model_Abstract {
	
	protected $_resource;
	protected $_messages = array();
	
	public function __construct() {
		$this->_setResource($resource = Seven::getResource("core/session_file"));
		if(PHP_SAPI !== 'cli') // don't start session in cli mode
			$resource->start();
		$this->_data = &$resource->data;
		$this->_messages = parent::getMessages();
	}
	
	protected function _getResource() {
		return $this->_resource;
	}
	
	protected function _setResource($resource) {
		$this->_resource = $resource;
		return $this;
	}
	
	public function addMessage($message, $type = "info") {
		if(!isset($this->_messages[$type])) $this->_messages[$type] = array();
		$this->_messages[$type][] = $message;
		$this->setMessages($this->_messages);
		return $this;
	}
	
	public function addSuccess($message) {
		return $this->addMessage($message, 'success');
	}

	public function addError($message) {
		return $this->addMessage($message, 'error');
	}
	
	public function addWarning($message) {
		return $this->addMessage($message, 'warning');
	}
	
	public function addInfo($message) {
		return $this->addMessage($message, 'info');
	}
	
	public function getMessages($type) {
		return isset($this->_messages[$type]) ? $this->_messages[$type] : array();
	}
	
	public function flushMessages($type) {
		unset($this->_data['messages'][$type]);
	}
	
	protected $_locale = null;
	
	public function getLocale() {
		if(! $this->_locale) {
			$locales = array(parent::getLocale(), Seven::getSiteConfig("general/site/language"), 'en-US');
			foreach($locales as $locale_code) {
				$this->_locale = Seven::getModel('core/locale')->load($locale_code);
				if($this->_locale->_getId())
					break;
			}
		}
		return $this->_locale;
	}
	
	public function setLocale($locale) {
		if($locale instanceof Core_Model_Locale) {
			$this->_locale = $locale;
			$locale = $locale->_getId();
		}
		parent::setLocale($locale);
	}

}