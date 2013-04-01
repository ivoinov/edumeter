<?php

class Developer_Model_Integritycheck_Task_Entity_Tables extends Developer_Model_Integritycheck_Task_Abstract {
		
		/**
		 * @see Developer_Model_Integritycheck_Task_Abstract::check()
		 */
		
		public function check($context = null) {
			if($context === null && $context instanceof Developer_Model_Config_Entity)
				throw new Exception('Config entity model should be passed');
				
			$report = Seven::getModel('developer/integritycheck_report')->setMessage("Table schemes");
			try {
				$tables = $context->getEntityTables();
			} catch(Exception $e) {
				$report->addSubreport('error', $this->_reportWarning(__('Unable to get table list')));
				return $report;
			}
			
			foreach($tables as $tablename) {
				$report->addSubreport($tablename, $this->_checkTable($context, $tablename));
			}
			return $report;
		}
		
		/**
		 * Check table
		 * 
		 * @param Developer_Model_Config_Entity $entity
		 * @param string $tablename
		 * @return Developer_Model_Integritycheck_Report
		 */
		
		protected function _checkTable(Developer_Model_Config_Entity $entity, $tablename) {
			$report = $this->_report(__('Table `%s`', $tablename));
			$scheme = $this->_buildTableScheme($entity, $tablename);
			
			if($this->_isTableExists($entity, $tablename)) {
				$fields = $this->_getTableFields($entity, $tablename);
				
				$keys   = array_unique(array_merge(array_keys($scheme), array_keys($fields)));
				
				foreach($keys as $key){
					$report->addSubreport($key, $this->_checkField(
							isset($fields[$key]) ? $fields[$key] : null, 
							isset($scheme[$key]) ? $scheme[$key] : null
					));
				}
				
				$report->addSubreport('__indexes', Seven::getModel('developer/integritycheck_task_entity_tables_indexes')
							->check(new Seven_Object(array('table_name' => $tablename, 'entity'     => $entity))));
			} else {
				$helper = Seven::getHelper('developer/integritycheck_database');
				$report->addSubreport('notexists', 
							$this->_reportError(__("Table not exists"))
								->setHowtoFix($helper->buildCreateTableQuery($tablename, $scheme)));
			}
			return $report;
		}
		
		/**
		 * Check field in table
		 * @param Seven_Object $field
		 * @param Seven_Object $scheme 
		 * @return Developer_Model_Integritycheck_Report
		 */
		
		protected function _checkField($field, $scheme) {
			if(!$field && !$scheme)
				return $this->_reportError('Call _checkFields without parameters');
			
			$helper = Seven::getHelper('developer/integritycheck_database');
			$report = $this->_report($field ? $field->getColumnName() : $scheme->getColumnName());
				
			if($field === null) {
				$report->addSubreport('resolution', $this->_reportError(__('Field not stored in this table')));
				$report->setHowtoFix($helper->buildAddFieldQuery($scheme));
			} else if($scheme === null) {
				$report->addSubreport('resolution', $this->_reportError(__('Field not defined')));
				$report->setHowtoFix($helper->buildDropFieldQuery($field));
			} else {
				foreach($scheme->getData() as $key => $value) {
					if($key == 'length' && $value === null)
						$value = $field->getData($key);
					
					if($field->getData($key) != $value)
						$report->addSubreport($key, $this->_reportWarning("<b>{$key}</b>: <i>" . json_encode($field->getData($key)) . "</i> instead of <i>" . json_encode($value) . "</i>"));
					else
						$report->addSubreport($key, $this->_report("<b>{$key}</b>: <i>" . json_encode($value) . "</i>"));
				}
				if($report->getState() != Developer_Model_Integritycheck_Report::STATE_OK)
					$report->setHowtoFix($helper->buildChangeFieldQuery($scheme));
			}
			return $report;
		}

		/**
		 * Return list of table fields 
		 * 
		 * @param Developer_Model_Config_Entity $entity
		 * @param string $tablename
		 * @return array
		 */
		
		protected function _getTableFields($entity, $tablename) {
			$helper = Seven::getHelper('developer/integritycheck_database');
			$fields = array();
			foreach($entity->getEntityTableFields($tablename) as $key => $description) {
				$fields[$key] = $helper->convFieldDescription($description);
			}
			return $fields;
		}


		/**
		 * Return list of table fields according to XML definition
		 *
		 * @param Developer_Model_Config_Entity $entity
		 * @param string $tablename
		 * @return array
		 */
		
		protected function _buildTableScheme(Developer_Model_Config_Entity $entity, $tablename) {
			$helper = Seven::getHelper('developer/integritycheck_database');
			$scheme = array();
			$index  = 0;
			
			foreach($entity->getEntityAttributes() as $key => $attribute) {
				if($attribute->getInternal()) 
					continue;
					
				$storagename = $attribute->getTableName() ? : (($attribute->getMultiview() && $entity->getViewsTable()) ? $entity->getViewsTable() : $entity->getEntityTable());
				if($storagename != $tablename && $key != $entity->getPrimaryKey())
					continue;
				
				$incrementable = $attribute->getIncrementable() === NULL || $attribute->getIncrementable(); 
				
				$scheme[$key] = new Seven_Object(array(
//					'scheme_name'		=> NULL,
					'table_name' 		=> $tablename,
					'column_name' 		=> $key,
//					'column_position' 	=> ++$index,
					'data_type'			=> $attribute->getDataType(),
					'default'			=> $attribute->getDefault(),
					'nullable'			=> ($attribute->getNullable() === NULL) ? $attribute->getMultiview() : $attribute->getNullable(),
					'length'			=> $helper->isLengthApplicable($attribute->getDataType()) ? $attribute->getDataLength() : null,
					'scale'				=> $attribute->getScale(),
					'precision'			=> $attribute->getPrecision(),
					'unsigned'			=> (bool)$attribute->getUnsigned(),
					'primary'			=> $key == $entity->getPrimaryKey() || ($key == '_view_id' && $attribute->getMultiview()),
					'identity'			=> $tablename == $entity->getEntityTable() && $incrementable && $key == $entity->getPrimaryKey() && $helper->isIncrementableType($attribute->getDataType())
				));
				
			}
			
			return $scheme;
		}
		
		/**
		 * 
		 */
		
		protected function _isTableExists(Developer_Model_Config_Entity $entity, $tablename) {
			return count($entity->getEntityResource()->getConnection()->query("SHOW TABLES LIKE ?", $tablename)->fetchAll());
		}
	}
