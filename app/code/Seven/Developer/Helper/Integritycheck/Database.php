<?php 

	class Developer_Helper_Integritycheck_Database {
		
		public function isSimilarType($type1, $type2) {
			$similar = array(
				array('TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'REAL', 'BIT', 'BOOLEAN', 'SERIAL'),
				array('DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR'),
				array('CHAR', 'VARCHAR', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT'),
				array('BINARY', 'VARBINARY', 'TINYBLOB', 'MEDIUMBLOB', 'BLOB', 'LONGBLOB'),
			);
			foreach($similar as $types) {
				if(in_array(strtoupper($type1), $types))
					return in_array(strtoupper($type2), $types);
			}
			return $type1 == $type2;			
		}
		
		public function isLengthApplicable($type) {
			return in_array(strtoupper($type), array('CHAR', 'VARCHAR', 'BINARY', 'VARBINARY'));			
		}
		
		public function isIncrementableType($type) {
			return in_array(strtoupper($type), array('TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT'));
		}

		public function isScaleApplicable($type) {
			return in_array(strtoupper($type), array('TINYINT', 'SMALLINT', 'MEDIUMINT', 'INT', 'BIGINT', 'DECIMAL', 'REAL', 'BIT', 'BOOLEAN', 'SERIAL'));
		}

		public function isPrecisionApplicable($type) {
			return in_array(strtoupper($type), array('DECIMAL', 'DOUBLE', 'REAL', 'BIT'));
		}
		
		public function buildDropFieldQuery($scheme) {
			return sprintf("ALTER TABLE `%s` DROP `%s`;", $scheme->getTableName(), $scheme->getColumnName());
		}
		
		public function buildAddFieldQuery($scheme) {
			return sprintf('ALTER TABLE `%s` ADD `%s` %s;',
							$scheme->getTableName(),
							$scheme->getColumnName(),
							$this->_buildFiledTypeString($scheme)
			);
		}

		public function buildChangeFieldQuery($scheme) {
			return sprintf('ALTER TABLE `%s` CHANGE `%s` `%s` %s;',
							$scheme->getTableName(),
							$scheme->getColumnName(),
							$scheme->getColumnName(),
							$this->_buildFiledTypeString($scheme)
			);
		}
		
		protected function _buildFiledTypeString($scheme) {
			$type = $scheme->getDataType();
				
			if($this->isLengthApplicable($scheme->getDataType()))
				$type .= "(" . ($scheme->getLength() ?: 255) . ")";
			
			if($this->isScaleApplicable($scheme->getDataType())) {
				if($this->isPrecisionApplicable($scheme->getDataType()))
					$type .= "(" . ($scheme->getPrecision() ?: 12) . ", " . ($scheme->getScale() ?: 4) . ")";
				else 
					$type .= "(" . ($scheme->getScale() ?: 11) . ")";
			}
			
			return sprintf("%s%s%s%s",
					strtoupper($type) ?: 'VARCHAR(255)',
					$scheme->getNullable() ? ' NULL' : ' NOT NULL',
					($scheme->getDefault() !== null) ? (' DEFAULT "' . $scheme->getDefault() . '"') : "",
					$scheme->getIdentity() ? " AUTO_INCREMENT" : ""
			);
		}
		
		public function buildCreateTableQuery($tablename, $scheme) {
			$fields = array();
			$pk		= array();
			
			foreach($scheme as $field_scheme) {
				$fields[] = sprintf("      `%s` %s", $field_scheme->getColumnName(), trim($this->_buildFiledTypeString($field_scheme)));
				if($field_scheme->getPrimary())
					$pk[] = $field_scheme->getColumnName();
			}
			if($pk)
				$fields[] = sprintf("      PRIMARY KEY (`%s`)", implode('`, `', $pk));
			
			return sprintf("CREATE TABLE `%s` (\n%s\n) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;", $tablename, implode(",\n", $fields));
		}
		
		public function convFieldDescription($description) {
    		$definition = new Seven_Object();
    		foreach($description as $key => $value)
    			$definition->setData(strtolower($key), $value);
    		return $definition;
		}
		
	}