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

    class Core_Block_Template extends Core_Block_Abstract {
        
    	protected function _toHtml() {
    		return $this->render($this->getTemplate());
    	}
    	
    	protected function render($template) {
            ob_start();
            $template = str_replace('/', DS, $template);
            $area = $this->getLayout()->getArea();
            $theme = $this->getLayout()->getTheme();
            $include_path = false;
                        
            // lookup in package design
            $path = explode(DS, dirname($template), 3);
            if(count($path) >= 2) {
            	list($pool, $package, $_template) = explode(DS, $template, 3);
            	foreach(Seven::getConfig('design/paths', $area) as $path) {
            		foreach(array('default', $theme) as $_theme) {
            			$template_path = implode(DS, array(BP, "app", "code", $pool, $package, str_replace('/', DS, trim($path, '/')), $_theme, "templates", $_template));
            			if(is_file($template_path))
            				$include_path = $template_path;
            		}
            	}
            }
            
            // lookup in app design 
            foreach(Seven::getConfig('design/paths', $area) as $path) {
            	foreach(array('default', $theme) as $_theme) {
            		$template_path = implode(DS, array(BP, "app", str_replace('/', DS, trim($path, '/')), $_theme, "templates", $template));
            		if(is_file($template_path))
            			$include_path = $template_path;
            	}
            }

            if(!empty($include_path))
            	include($include_path);
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }

    }
