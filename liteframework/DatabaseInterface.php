<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Database Interface
 *
 * @modified : 26 Aug 2022
 * @created  : 02 Jun 2020
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface DatabaseInterface
{
	/**
	 * Initializes the database settings
	 *
	 * @param array
	 *    'drive'     => 'sqlite|mysql|mariadb',
     *    'filename'  => 'sqlite_database_file_path.db',
     *    'name'      => 'db_name',
     *    'server'    => '127.0.0.1',
     *    'port'      => 3306,
     *    'username'  => '',
     *    'password'  => '',
	 *    'prefix'    => '',
	 *    'logging'   => false,
     *    'charset'   => 'utf8mb4',
     *    'collation' => 'utf8mb4_general_ci',
	 *
	 * @return bool true on success/false on failure
	*/
	public function setConfig(array $config) : bool;

	/**
	 * Get databae driver name
	 *
	 * @return string sqlite|mysql|mariadb
	*/
	public function getDrive() : string;

	/**
	 * Connect to the database
	 *
	 * @return bool true on success/false on failure
	*/
	public function connect() : bool;

	/**
	 * Transaction start
	 *
	 * @return bool true on success/false on failure
	*/
	public function transStart() : bool;

	/**
	 * Transaction commit
	 *
	 * @return bool true on success/false on failure
	*/
	public function transCommit() : bool;

	/**
	 * Transaction rollback
	 *
	 * @return bool true on success/false on failure
	*/
	public function transRollback() : bool;

	/**
	 * Get error
	 *
	 * @return mixed
	*/
	public function errorInfo();

/*
	// Medoo API //

	public function query($query, $map = []);

	public function exec($query, $map = []);

	public static function raw($string, $map = []);

	public function quote($string);

	public function create($table, $columns, $options = null);

	public function drop($table);

	public function select($table, $join, $columns = null, $where = null);

	public function insert($table, $datas);

	public function update($table, $data, $where = null);

	public function delete($table, $where);

	public function replace($table, $columns, $where = null);

	public function get($table, $join = null, $columns = null, $where = null);

	public function has($table, $join, $where = null);

	public function rand($table, $join = null, $columns = null, $where = null);

	public function count($table, $join = null, $column = null, $where = null);

	public function avg($table, $join, $column = null, $where = null);

	public function max($table, $join, $column = null, $where = null);

	public function min($table, $join, $column = null, $where = null);

	public function sum($table, $join, $column = null, $where = null);

	public function action($actions);

	public function id();

	public function debug();

	public function error();

	public function last();

	public function log();

	public function info();
*/
}
