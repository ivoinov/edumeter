<?php

    class Core_Model_Layout_Renderer_Direct extends Core_Model_Layout_Renderer_Abstract {

        /**
         * @todo Implement target stream
         * @param $stream
         */

        public function setOutputStream($stream) {

        }

        /**
         * @param Core_Model_Layout $layout
         * @return string
         */

        public function render(Core_Model_Layout $layout) {
            return $layout->getRootBlock()->toHtml();
        }

    }