<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * SQL table builder
 *
 * @modified : 01 Feb 2022
 * @created  : 29 Jan 2016
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

use LiteFramework\SqlTableBuilderInterface;

defined('LITEF_PATH') OR exit('Restricted access');

class SqlTableBuilder implements SqlTableBuilderInterface
{
	protected $drive   = 'mysql';
	protected $schema  = [];
	protected $sqlCode = [];

	protected $tablePrefix = null;
	protected $ifNotExists = true;
	protected $primary_key = null;
	protected $uniqueIndexes = [];
	protected $tableOptions  = [];
	protected $foreignKeys   = [];
	protected $error = null;

	/**
	 * SQL-Schema
	 *
	 * @param  array
	 * @return bool false on failure/true on success
	*/
	public function setSchema(array $schema) : bool
	{
		$this->schema = $schema;
		return true;
	}

	/**
	 * SQL-Schema File
	 *
	 * @param  string file path
	 * @return bool false on failure/true on success
	*/
	public function loadSchemaFromFile(string $filename) : bool
	{
		if (is_file($filename) === false) {
			$this->error = 'schema_file_not_found';
			return false;
		}

		$this->schema = require($filename);
		if (is_array($this->schema) === false) {
			$this->error = 'schema_file_invalid_contents['.$filename.']';
			return false;
		}

		return true;
	}

	/**
	 * Build
	 *
	 * @param  string $dive  mysql|mariadb|sqlite
	 * @return array on success/bool false on failure
	*/
	public function build(string $drive = 'mysql')
	{
		$drive = strtolower(trim($drive));
		if ($drive === 'mariadb')
			$drive = 'mysql';

		if ($drive !== 'mysql' && $drive != 'sqlite') {
			$this->error = 'drive_is_not_supported['.$drive.']';
			$this->drive = false;
			return false;
		}

		$this->drive = $drive;

		if (count($this->schema) === 0) {
			$this->error = 'invalid schema 2';
			return false;
		}

		$sql = $this->builder();
		if ($sql === false) {
			if ($this->error === null)
				$this->error = 'unknown_error';
			return false;
		}

		return $this->sqlCode;
	}

	/**
	 * Set Table Prefix
	 *
	 * @param  string
	 * @return void
	*/
	public function setTablePrefix(string $prefix) : void
	{
		$this->tablePrefix = $prefix;
		return;
	}

	/**
	 * Set table if not exists
	 *
	 * @param  bool
	 * @return void
	*/
	public function createIfNotExists(bool $rc) : void
	{
		$this->ifNotExists = $rc;
		return;
	}

	/**
	 * Get error
	 *
	 * @return string|null
	*/
	public function errorInfo()
	{
		return $this->error;
	}

	/**
	 * SQL Builder
	 *
	 * @return bool false on failure/true on success
	*/
	protected function builder()
	{
		foreach ($this->schema as $tableName => $tableData) {
			// New Table
			$addNew = $this->createTable($tableName, $tableData);
			if ($addNew === false)
				return false;
		}

		return true;
	}

	/**
	 * Create New Table
	 *
	 * @param  string
	 * @param  array
	 * @return bool false on failure/string on success
	*/
	protected function createTable(string &$tableName, array &$tableData)
	{
		$ifNot = $this->ifNotExists === true ? 'IF '.'NOT '.'EXISTS' : null;
		$sqlCode = 'CREATE' . ' TABLE '.$ifNot.' `'.$this->tablePrefix . $tableName.'` (' . "\n";

		// Reset
		$this->primary_key = null;
		$this->uniqueIndexes = [];
		$this->foreignKeys = [];

		// Columns
		foreach ($tableData as $columnName => $data) {
			// Tale Indexes
			if ($columnName === '_index')
				continue;

			// Table Options
			if ($columnName === '_options') {
				$this->tableOptions[$tableName] = $data;
				continue;
			}

			// Map (no col)
			if ($columnName[0] === '_')
				continue;

			$col = $this->createColumn($tableName, $columnName);
			if ($col === false)
				return false;

			$sqlCode .= $col . "\n";
		}

		// Indexes (MySQL)
		if ($this->drive === 'mysql')
			$sqlCode .= $this->addMysqlTableIndexes($tableName);

		// Foreign Key
		if (count($this->foreignKeys) !== 0) {
			if ($this->drive === 'mysql')
				$sqlCode .= $this->addForeignKey($tableName);
			else if ($this->drive === 'sqlite')
				$sqlCode .= $this->addForeignKey($tableName);
		}

		// Close
		$sqlCode = rtrim(trim($sqlCode), ',');

		// END - MySQL
		if ($this->drive === 'mysql') {
			$opt = $this->addTableOptions($tableName);
			$sqlCode .= ')' . $opt . ';';
			$this->sqlCode[] = $sqlCode;
		}
		// END - SQLite
		else if ($this->drive === 'sqlite') {
			$sqlCode .= ');';
			$this->sqlCode[] = $sqlCode;
			$this->addSqliteTableIndexes($tableName);
		}

		return true;
	}

	/**
	 * Create New Column
	 *
	 * @param  string
	 * @param  string
	 * @return bool false on failure/string on success
	*/
	protected function createColumn(string &$tableName, string &$columnName)
	{
		// Schema
		if (isset($this->schema[$tableName][$columnName]) === false) {
			$this->error = 'invalid schema tabal_column';
			return false;
		}

		$schema = $this->schema[$tableName][$columnName];

		// Open
		$sqlCode = '  `'.$columnName.'` ';

		// Type
		if (isset($schema['type']) === false || empty($schema['type']) === true) {
			$this->error = 'invalid_column_type['.$columnName.']';
			return false;
		}
		else {
			$type = $this->rigidType($schema['type']);
			if ($type == false) {
				$this->error = 'invalid sql type::'.$columnName.'::'.$schema['type'].'';
				return false;
			}

			$sqlCode .= $type .' ';
		}

		// Unsigned
		if ($this->drive !== 'sqlite' && isset($schema['unsigned']) === true && $schema['unsigned'] === true)
			$sqlCode .= 'UNSIGNED ';

		// If Default
		$is_default = true === array_key_exists('default', $schema) ? true : false;

		// Null
		if (true === $is_default && is_null($schema['default']) === true)
			$schema['null'] = true;

		if (isset($schema['null']) === true && true === $schema['null']) {
			$notNull = false;
		}
		else {
			$notNull = true;
			$sqlCode .= 'NOT NULL ';
		}

		// Primary Key
		if (isset($schema['primary_key']) === true && true === $schema['primary_key'])
			$sqlCode .= $this->addColPrimaryKey($columnName);

		// Auto Increment
		if (isset($schema['autoincrement']) && true === $schema['autoincrement'])
			$sqlCode .= $this->addAutoIncrement();

		// Unique Index
		if (isset($schema['unique']) && false !== $schema['unique'])
			$sqlCode .= $this->addUniqueIndex($columnName, $schema['unique']);

		// Default
		if (true === $is_default)
			$sqlCode .= $this->addDefaultValue($schema['default'], $notNull);

		// Foreign Keys
		if (isset($schema['foreign_key']) && false !== $schema['foreign_key'])
			$this->foreignKeys[$tableName][] = $schema['foreign_key'];

		// Done
		return rtrim($sqlCode) . ',';
	}

	/**
	 * Col Primary Key
	 *
	 * @param  string
	 * @return string
	*/
	protected function addColPrimaryKey(string &$columnName) : string
	{
		if ($this->drive === 'mysql') {
			$this->primary_key = $columnName;
			return '';
		}
		else if ($this->drive === 'sqlite') {
			$this->primary_key = null; // reset
			return 'PRIMARY KEY ';
		}

		return '';
	}

	/**
	 * Auto Increment
	 *
	 * @return string
	*/
	protected function addAutoIncrement() : string
	{
		if ($this->drive === 'mysql')
			return 'AUTO_INCREMENT ';
		else if ($this->drive === 'sqlite')
			return 'AUTOINCREMENT ';
		return '';
	}

	/**
	 * Unique Index
	 *
	 * @return string
	*/
	protected function addUniqueIndex(&$columnName, string $sort = 'ASC') : string
	{
		if ($this->drive === 'mysql') {
			$sort = strtoupper($sort);
			$this->uniqueIndexes[$columnName] = ('DESC' === $sort) ? 'DESC' : 'ASC';
			return '';
		}
		else if ($this->drive === 'sqlite') {
			return 'UNIQUE ';
		}

		return '';
	}

	/**
	 * Default Value
	 *
	 * @param  mixed
	 * @param  string|int
	 * @return string
	*/
	protected function addDefaultValue(&$default, &$notNull) : string
	{
		if (is_null($default) === true && $notNull == false)
			return 'DEFAULT NULL ';
		else if (is_bool($default) === true && $default == true)
			return 'DEFAULT 1 ';
		else if (is_bool($default) === true && $default == false)
			return 'DEFAULT 0 ';
		else if (is_int($default) === true)
			return "DEFAULT ".$default." ";
		else if (is_string($default) === true || is_numeric($default) === true)
			return "DEFAULT '".$default."' ";
		return '';
	}

	/**
	 * Add - MySQL Table Indexes
	 *
	 * @param  string
	 * @return string
	*/
	protected function addMysqlTableIndexes(string &$tableName) : string
	{
		if ($this->drive != 'mysql')
			return '';

		$sqlCode = '';

		// MySQL Primary_key
		if (is_string($this->primary_key) === true)
			$sqlCode .= '  PRIMARY KEY (`'.$this->primary_key.'`),' . "\n";

		// MySQL Unique Indexes
		if (count($this->uniqueIndexes) !== 0) {
			foreach ($this->uniqueIndexes as $name => $sort) {
				$name = strtoupper($name);
				$sqlCode .= '  UNIQUE INDEX `'.$name.'_UNIQUE` (`'.$name.'` '.$sort.'),' . "\n";
			}
		}

		// MySQL All Indexes
		if (isset( $this->schema[$tableName]['_index'] ))
			$sqlCode .= $this->addMysqlIndex($tableName, $this->schema[$tableName]['_index']);

		return $sqlCode;
	}

	/**
	 * Add - Sqlite Table Indexes
	 *
	 * @param  string
	 * @return string
	*/
	protected function addSqliteTableIndexes(&$tableName)
	{
		if ($this->drive !== 'sqlite')
			return '';

		// ADD
		if (isset($this->schema[$tableName]['_index']) === false || 
			empty($this->schema[$tableName]['_index']) === true) {
			return '';
		}

		$schema = $this->schema[$tableName]['_index'];
		$tname = $this->tablePrefix.$tableName;
		$sqlCode = '';

		// ADD
		foreach ($schema as $key => $indexes) {
			if (is_array($indexes) === false) {
				if (is_string($indexes) === false) {
					echo 'error_invalid_sql_index['.$key.']';
					exit(0);
				}
				$indexes = [$indexes => 'ASC'];
			}

			$is_unique = isset($indexes['unique']) ? 'UNIQUE' : null;
			$ind = '';
			$ind .= 'CREATE '.$is_unique.' INDEX IF NOT EXISTS `'.$tname.'_'.$key.'` ON `'.$tname.'` (';

			foreach ($indexes as $indexN => $value) {
				if ($indexN === 'unique')
					continue;

				$ind .= '`' . $indexN .'` '. strtoupper( $value ).',';
			}

			$this->sqlCode[] = rtrim(trim($ind), ',') . ");";
		}

		return true;
	}

	/**
	 * Add - MySQL Index
	 *
	 * @param  string
	 * @param  string
	 * @return string
	*/
	protected function addMysqlIndex(string &$tableName, &$schema) : string
	{
		$sqlCode = '';
		$tname = $this->tablePrefix.$tableName;
		foreach ($schema as $key => $indexes) {
			if (is_array($indexes) === false) {
				if (is_string($indexes) === false) {
					echo 'error_invalid_sql_index['.$key.']';
					exit(0);
				}
				$indexes = [$indexes => 'ASC'];
			}

			// if is Unique indexes
			if (isset($indexes['unique'])) {
				unset($indexes['unique']);
				$sqlCode .= ' UNIQUE';
			}

			$sqlCode .= ' INDEX `'.$tname.'_'.$key.'` (';
			$code = '';
			foreach ($indexes as $indexN => $value) {
				// Unique
				if ('unique' === $indexN)
					continue;

				// fix
				if (empty($indexN) === true)
					$indexN = $value;

				// set
				$val = strtoupper($value);
				$val = ('DESC' == $val) ? 'DESC' : 'ASC';
				$code .= '`'.$indexN .'` '. $val.',';
			}

			if (empty($code) === false)
				$sqlCode .= rtrim(trim($code), ',') . '),' . "\n";
		}

		return $sqlCode;
	}

	/**
	 * Add - Foreign Key
	 *
	 * @param  string
	 * @return string
	*/
	protected function addForeignKey(&$tableName) : string
	{
		$sqlCode = '';
		if (0 === count($this->foreignKeys))
			return $sqlCode;

		foreach ($this->foreignKeys as $key1 => $foreignKeys) {
			if (is_array($foreignKeys) === false)
				continue;

			foreach ($foreignKeys as $key => $data) {
				$fkName = 'fk_' . $this->tablePrefix . $data['ref_table'] . '_' . $tableName . '_' . $data['column'];

				$sqlCode .= "  CONSTRAINT `".$fkName."`\n";
				$sqlCode .= "   FOREIGN KEY (`".$data['column']."`)\n";
				$sqlCode .= "   REFERENCES `".$this->tablePrefix . $data['ref_table']."` (`".$data['ref_column']."`)\n";

				//  ON UPDATE
				$onUpdate = (isset($data['on_update']) && $data['on_update'] != false) ? strtoupper($data['on_update']) : false;
				if ($onUpdate == false || $onUpdate == 'NO ACTION')
					$sqlCode .= "   ON UPDATE NO ACTION\n";
				else
					$sqlCode .= "   ON UPDATE ".$onUpdate."\n";

				//  ON DELETE
				$onDelete = (isset($data['on_delete']) && false !== $data['on_delete']) ? strtoupper($data['on_delete']) : false;
				if ($onDelete === false || $onDelete === 'NO ACTION')
					$sqlCode .= "   ON DELETE NO ACTION";
				else
					$sqlCode .= "   ON DELETE ".$onDelete."";

				$sqlCode .= ",\n";
			}

			return $sqlCode;
		}
	}

	/**
	 * Add - Table Options
	 *
	 * @param  string
	 * @return string
	*/
	protected function addTableOptions(&$tableName)
	{
		$sqlCode = '';
		$options = isset($this->schema[$tableName]['_options']) ? $this->schema[$tableName]['_options'] : false;

		// MySQL
		if ($this->drive == 'mysql') {
			// Engine
			if (isset($options['engine']) && strtolower($options['engine']) === 'myisam')
				$sqlCode .= "\n ENGINE = MyISAM";
			else
				$sqlCode .= "\n ENGINE = InnoDB";

			// Auto Increment
			if (isset($options['auto_increment']))
				$sqlCode .= "\n AUTO_INCREMENT = " . $options['auto_increment'];

			// Character Set
			if (isset($options['character_set']))
				$sqlCode .= "\n DEFAULT CHARACTER SET = " . $options['character_set'];

			// Collate
			if (isset($options['collate']))
				$sqlCode .= "\n COLLATE = " . $options['collate'];
		}

		return $sqlCode;
	}

	/**
	 * Rigid Type
	 *
	 * @return bool false on failure/string on success
	*/
	protected function rigidType(&$input)
	{
		$type = $input = strtolower($input);
		$typeVal = null;

		$typeEx = explode('(', $input, 2);
		if (isset($typeEx[1]) === true) {
			$type = $typeEx[0];
			$typeVal = (int) trim($typeEx[1], ')');
		}

		// MySQL
		if ($this->drive === 'mysql') {
			switch($type) {
				case 'integer':
					$type = 'int';
				break;
				case 'character':
					$type = 'char';
				break;
			}
		}
		// SQLite
		else if ($this->drive === 'sqlite') {
			switch ($type) {
				case 'tinyint': case 'smallint': case 'mediumint': case 'int':  case 'bigint': 
					$type = 'integer';
					$typeVal = false;
				break;
				case 'char': case 'varchar':
					$type = 'text';
					$typeVal = false;
				break;
				case 'double': case 'float':
					$type = 'real';
					$typeVal = false;
				break;
				case 'date': case 'datetime':
					$type = 'numeric';
					$typeVal = false;
				break;
			}
		}

		// Set
		if ($typeVal)
			$type = strtoupper($type).'('.$typeVal.')';
		else
			$type = strtoupper($type);

		return $type;
	}
}

/**
 * Example:
 *
 * 'users' => [
 * 		'id' => [
 * 			'type'          => 'MEDIUMINT(2)',
 * 			'unsigned'      => true,
 * 			'autoincrement' => true,
 * 			'primary_key'   => true,
 * 		],
 * 		'group_id' => [
 * 			'type' => 'INT(10)',
 * 			'unsigned' => true,
 * 			'foreign_key' => [
 * 				'ref_table'  => 'groups',
 * 				'ref_column' => 'id',
 * 				'column'     => 'group_id',
 * 				'on_delete'  => 'cascade'
 * 			]
 * 		],
 * 		'name' => ['type' => 'varchar(100)', 'default' => null],
 * 		'_options' => ['engine' => 'InnoDB'],
 * 		'_index' => [
 * 			'example_index_name' => ['group_id' => 'asc']
 * 		],
 * ]
*/
