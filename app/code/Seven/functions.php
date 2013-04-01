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

    function real_class_alias($original, $alias) {
        if(empty($original) || empty($alias))
            return false;
        if(preg_match("/[^a-z_0-9]/i", $original) || preg_match("/[^a-z_0-9]/i", $alias))
    	    return false;
        eval('class ' . $alias . ' extends ' . $original . ' {}');
    }

    function __($text) {
    	if(!($website = Seven::app()->getWebsite())) return $text;
    	if(!($locale = $website->getLocale())) return $text;
        return call_user_func_array(array($locale, 'translate'), func_get_args());
    }

    function _e($text) {
        echo call_user_func_array('__', func_get_args());
    }

    function _n($text) {
        return call_user_func_array('__', func_get_args());
    }

    function _en($text) {
        return call_user_func_array('_e', func_get_args());
    }    
    
    function array_merge_recursive_replace($src, $dist) {
        foreach($dist as $key => $value) {
            if(isset($src[$key]) && is_array($value)) {
                $src[$key] = array_merge_recursive_replace(is_array($src[$key]) ? $src[$key] : array(), $value);
            } else {
               	$src[$key] = $value;
            }
        }
        return $src;
    }

    function _url($url, $args = array()) {
    	return seven_url($url, $args);
    } 
    
    function seven_url($url, $args = array()) {
        return Seven::getModel('core/url_builder')->build($url, $args);
    }

    /**
     * @return Core_Model_Debug
     */

    function debug() {
    	$debug = Seven::getObjectCache('__core_debug');
    	if(!isset($debug['instance']))
    		$debug['instance'] = new Core_Model_Debug();
    	return $debug['instance'];
    }
    
    function getRandomString($len, $chars=null) {
        if (is_null($chars))
            $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789~!@#$%^&*()_+-={}\\|/><,.?;:\"'";
        mt_srand(10000000*(double)microtime());
        for ($i = 0, $str = '', $lc = strlen($chars)-1; $i < $len; $i++)
            $str .= $chars[mt_rand(0, $lc)];
        return $str;
    }
    
    function call_seven_callback($callback, $callback_object_type) {
    	$args = func_get_args();
    	unset($args[key($args)]);
    	unset($args[key($args)]);
    	return call_seven_callback_array($callback, $args, $callback_object_type);
    }
    
    function call_seven_callback_array($callback, $args, $callback_object_type = NULL) {
    	if(is_string($callback) && strpos($callback, '::') !== false) {
    		list($object_class, $method) = explode('::', $callback);
    		$callback = array(Seven::getObjectByAlias($object_class, $callback_object_type), $method); 
    	}
    	return call_user_func_array($callback, $args);
    }
    
    function seven_image($path) {
    	return Seven::getModel('core/image')->load($path);
    }
    
    function seven_file($path) {
    	return Seven::getModel('core/file')->load($path);
    }
    
    function seven_mail($to, $template_id, $template_vars = array()) {
        return Seven::getModel('core/mail')
            ->addTo($to)
            ->setTemplate($template_id, $template_vars);
    }
    
    function seven_object($data) {
        return new Seven_Object($data);
    }
    