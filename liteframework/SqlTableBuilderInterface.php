<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * SQL table builder interface
 *
 * @modified : 15 Dec 2020
 * @created  : 29 Jan 2016
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface SqlTableBuilderInterface
{
	/**
	 * Set SQL-Schema
	 *
	 * @param  array
	 * @return bool false on failure/true on success
	*/
	public function setSchema(array $schema) : bool;

	/**
	 * Set the SQL schema file
	 *
	 * @param  string file path
	 * @return bool false on failure/true on success
	*/
	public function loadSchemaFromFile(string $filename) : bool;

	/**
	 * Set table prefix
	 *
	 * @param  string
	 * @return void
	*/
	public function setTablePrefix(string $prefix) : void;

	/**
	 * Create table if not exists
	 *
	 * @param  bool
	 * @return void
	*/
	public function createIfNotExists(bool $rc) : void;

	/**
	 * Build
	 *
	 * @param  string $dive  mysql|mariadb|sqlite
	 * @return array on success/bool false on failure
	*/
	public function build(string $drive = 'mysql');

	/**
	 * Get errors
	 *
	 * @return mixed
	*/
	public function errorInfo();
}
