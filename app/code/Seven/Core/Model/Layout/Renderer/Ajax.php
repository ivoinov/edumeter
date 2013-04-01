<?php

    class Core_Model_Layout_Renderer_Ajax extends Core_Model_Layout_Renderer_Http {

        /**
         * @todo Remove Core/Request dependency
         * @return Core_Model_Request
         */

        public function getRequest() {
            return Seven::app()->getRequest();
        }

        /**
         * @param Core_Model_Layout $layout
         * @return Core_Model_Response
         */

        public function render(Core_Model_Layout $layout) {
            $required = array_filter(array_map('trim', explode(',', $this->getRequest()->getParam('required'))));

            $response = $this->getResponse();
            /** @var $block Core_Block_Abstract */
            foreach($layout->getBlocks() as $block) {
                if($block->getUseAjax() && (empty($required) || in_array($block->getLayoutName(), $required)))
                    $response->addAjaxData($block->getAjaxName() ?: $block->getLayoutName(), $block->toAjax());
            }
        }

    }