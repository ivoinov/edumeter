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
 *
 */

    class Seven_Profiler {

        protected $_profile_stack;

        public function __construct() {
            $this->_profile_stack = array(new Seven_Object(array('childs' => new ArrayObject())));
        }

        public function open($point, $data = array()) {
            $node = new Seven_Object(array(
                'childs' => new ArrayObject(),
                'name'   => $point,
                'stime'  => microtime(true),
                'smemory'=> function_exists("memory_get_usage") ? memory_get_usage(true) : 0,
                'time'   => 0,
                'memory' => 0,
                'data'   => $data
            ));

            $current = end($this->_profile_stack);
            $current->getChilds()->append($node);
            $this->_profile_stack[] = $node;
        }

        public function close() {
            $current = end($this->_profile_stack);
            $current->setTime(microtime(true) - $current->getStime());
            if(function_exists("memory_get_usage"))
                $current->setMemory(memory_get_usage(true) - $current->getSmemory());
            array_pop($this->_profile_stack);
        }

        public function getProfileStack() {
            return reset($this->_profile_stack);
        }

        public function toHtml() {
            $profile = $this->getProfileStack();
            return "<div id='profiler'>" . self::_toHtml($profile->getChilds()) . "</div>";
        }

        protected function _toHtml($nodes, $level = 10) {
            if(!$nodes) return "";
            $str = "";
            foreach($nodes as $node) {
                $str .= "<li>";
                $str .= "<div class='profiler-row'><div class='profiler-row-time'>". (round($node->getTime() * 1000) / 1000) . "</div><div class='profiler-row-memory'>" . (round($node->getMemory() * 9.765625 / (($node->getMemory() > 1048576) ? 1024 : 1)) / 10000) . (($node->getMemory() > 1048576) ? " MB" : " KB") . "</div><div class='profiler-row-name'>" . $node->getName() . "</div><div class='profiler-row-data'>". json_encode($node->getData('data'), true) ."</div></div>";
                if($level != 0)
                    $str .= "<div class='profiler-row-childs'>". self::_toHtml($node->getChilds(), $level - 1) . "</div>";
                $str .= "</li>";
            }
            return "<ul>" . $str . "</ul>";
        }

    }
