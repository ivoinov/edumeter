<?php

class Developer_Model_Integritycheck_Task_Entity_Classes extends Developer_Model_Integritycheck_Task_Abstract {
		
		/**
		 * @see Developer_Model_Integritycheck_Task_Abstract::check()
		 */
		
		public function check($context = null) {
			if($context === null && $context instanceof Developer_Model_Config_Entity)
				throw new Exception('Config entity model should be passed');
				
			$report = $this->_report(__("Entity classes"));
			// model
			$class_name = Seven::getClassByAlias($context->_getId(), 'model');
			$class_report = $this->_report(__("Model <b>%s</b>", $class_name)); 
			if(!class_exists($class_name)) {
				$extends = $context->getViews() ? "Core_Model_Multiview_Entity" : "Core_Model_Entity";
				$class_report->setState(Developer_Model_Integritycheck_Report::STATE_ERROR)
					->setHowtoFix("<i>..." . DS . str_replace('_', DS, $class_name) . ".php:</i>\n\n&lt;?php\n\n\tclass {$class_name} extends {$extends} {\n\n\t}\n\n");
			}
			$report->addSubreport('model', $class_report);
			// resource
			$class_name = Seven::getClassByAlias($context->_getId(), 'resource');
			$class_report = $this->_report(__("Resource <b>%s</b>", $class_name));
			if(!class_exists($class_name)) {
				$extends = $context->getViews() ? "Core_Resource_Multiview_Entity" : "Core_Resource_Entity";
				$class_report->setState(Developer_Model_Integritycheck_Report::STATE_ERROR)
					->setHowtoFix("<i>..." . DS . str_replace('_', DS, $class_name) . ".php:</i>\n\n&lt;?php\n\n\tclass {$class_name} extends {$extends} {\n\n\t}\n\n");
			}
			$report->addSubreport('resource', $class_report);
			// collection
			$class_name = Seven::getClassByAlias($context->_getId(), 'resource') . "_Collection";
			$class_report = $this->_report(__("Collection <b>%s</b>", $class_name)); 
			if(!class_exists($class_name)) {
				$extends = $context->getViews() ? "Core_Resource_Multiview_Entity_Collection" : "Core_Resource_Entity_Collection";
				$class_report->setState(Developer_Model_Integritycheck_Report::STATE_ERROR)
					->setHowtoFix("<i>..." . DS . str_replace('_', DS, $class_name) . ".php:</i>\n\n&lt;?php\n\n\tclass {$class_name} extends {$extends} {\n\n\t}\n\n");
			}
			$report->addSubreport('collection', $class_report);
			return $report;
		}
		
	}
