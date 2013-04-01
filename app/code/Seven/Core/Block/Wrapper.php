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

    class Core_Block_Wrapper extends Core_Block_Abstract {
        
        public function wrapHtml($html) {
            $tag = $this->getHtmlTag() ? strtolower($this->getHtmlTag()) : "div";
            return "<{$tag} ".$this->getAttributeString().">".$html."</{$tag}>";
        }
        
        protected function _toHtml() {
            return $this->wrapHtml($this->getSortedChildrenHtml());
        }
        
    }