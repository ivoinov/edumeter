<?php 

	class Developer_Model_Integritycheck_Task_Entity_Tables_Indexes extends Developer_Model_Integritycheck_Task_Abstract {
		
		public function check($context = null) {
			$entity = $context->getEntity();
			$tablename = $context->getTableName();

			$pk = array($entity->getPrimaryKey());
			
			if($entity->isMultiview()) {
				if(!$entity->getViewsTable() || $tablename == $entity->getViewsTable())
					$pk[] = '_view_id';
			}
			
			return $this->_checkPrimaryKey($entity, $tablename, $pk);
		}
		
		
		public function _checkPrimaryKey($entity, $table, $keys) {
			$report = Seven::getModel('developer/integritycheck_report')
						->setMessage(__('PRIMARY KEY `%s`', implode('`, `', $keys)));
	
			$table_fields = $entity->getEntityTableFields($table);
			$current_keys = array();
			foreach($table_fields as $key => $desc) {
				if(!empty($desc['PRIMARY']))
					$current_keys[] = $key;
			}
			
			if($current_keys && $keys && array_diff($keys, $current_keys))
				$report->setMessage(__("Incrrect PRIMARY KEY `%s`", implode('`, `', $current_keys)))
					->setHowtoFix($this->_fix('sql')->setQuery(sprintf("ALTER TABLE `%s` DROP PRIMARY KEY;\nALTER TABLE  `%s` ADD PRIMARY KEY (`%s`)", $table, $table, implode('`, `', $keys))))
					->setState(Developer_Model_Integritycheck_Report::STATE_ERROR);
			
			if($keys && !$current_keys)
				$report->setMessage(__("Have no PRIMARY KEY"))
					->setHowtoFix($this->_fix('sql')->setQuery(sprintf("ALTER TABLE  `%s` ADD PRIMARY KEY (`%s`)", $table, implode('`, `', $keys))))
					->setState(Developer_Model_Integritycheck_Report::STATE_ERROR);
				
			return $report;
		}
		
	}