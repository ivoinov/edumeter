<?php

    class Core_Model_Layout_Renderer_Http extends Core_Model_Layout_Renderer_Abstract {

        /**
         * @var null
         */

        protected $_response = null;

        /**
         * @param Core_Model_Response $response
         */

        public function setResponse(Core_Model_Response $response) {
            $this->_response = $response;
        }

        /**
         * @return Core_Model_Response|null
         */

        public function getResponse() {
            if(!$this->_response)
                $this->_response = Seven::app()->getResponse();
            return $this->_response;
        }

        /**
         * @param Core_Model_Layout $layout
         * @return Core_Model_Response
         */

        public function render(Core_Model_Layout $layout) {
            return $this->getResponse()->addBody($layout->getRootBlock()->toHtml());
        }

    }