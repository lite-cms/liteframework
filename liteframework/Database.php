<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Database
 *
 * @modified : 26 Aug 2022
 * @created  : 06 Dec 2019
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

use LiteFramework\DatabaseInterface;

defined('LITEF_PATH') OR exit('Restricted access');

class Database implements DatabaseInterface
{
	// @object - Medoo class
	public $db = null;

	// @array
	protected $config = [];

	// @mixed
	protected $error = null;

	/**
	 * Initializes the database settings
	 *
	 * @param array $config
	 * @return true on success/false on failure
	*/
	public function setConfig(array $config) : bool
	{
		if (isset($config['drive'], $config['database']) === false) {
			$this->error = 'invalid_config';
			return false;
		}

		$this->config = $config;
		return true;
	}

	/**
	 * Get database drive name
	 *
	 * @return string
	*/
	public function getDrive() : string
	{
		return isset($this->config['drive']) ? $this->config['drive'] : '';
	}

	/**
	 * Call medoo
	 *
	 * @return string|null
	*/
	public function __call($name, $args)
	{
		if ($this->db === null) {
			$rc = $this->connect();
			if ($rc === false)
				return false;
		}

		$params_count = count($args);
		if ($params_count === 0)
			return $this->db->{$name}();
		else if ($params_count === 1)
			return $this->db->{$name}($args[0]);
		else if ($params_count === 2)
			return $this->db->{$name}($args[0], $args[1]);
		else if ($params_count === 3)
			return $this->db->{$name}($args[0], $args[1], $args[2]);
		else if ($params_count === 4)
			return $this->db->{$name}($args[0], $args[1], $args[2], $args[3]);

		return $this->db->{$name}($args);
	}

	/**
	 * Connect to the database
	 *
	 * @return true on success/false on failure
	*/
	public function connect() : bool
	{
		if ($this->db !== null)
			return true;

		$drive = isset($this->config['drive']) === false ? '' : strtolower($this->config['drive']);

		if ($drive === 'sqlite') {
			$this->db = new \Medoo\Medoo([
				'type'      => 'sqlite',
				'database'  => $this->config['database'],
				'charset'   => isset($this->config['charset']) ? $this->config['charset'] : 'utf8mb4',
				'collation' => isset($this->config['collation']) ? $this->config['collation'] : 'utf8mb4_general_ci',
				'prefix'    => isset($this->config['prefix']) ? $this->config['prefix'] : false,
				'logging'   => isset($this->config['logging']) ? $this->config['logging'] : false,
				'error'     => \PDO::ERRMODE_SILENT,
			]);
		}
		else if ($drive === 'mysql' || $drive === 'mariadb') {
			$this->db = new \Medoo\Medoo([
				'type'      => 'mysql',
				'database'  => $this->config['database'],
				'server'    => $this->config['server'],
				'username'  => $this->config['username'],
				'password'  => $this->config['password'],
				'charset'   => isset($this->config['charset']) ? $this->config['charset'] : 'utf8mb4',
				'collation' => isset($this->config['collation']) ? $this->config['collation'] : 'utf8mb4_general_ci',
				'prefix'    => isset($this->config['prefix']) ? $this->config['prefix'] : false,
				'logging'   => isset($this->config['logging']) ? $this->config['logging'] : false,
				'error'     => \PDO::ERRMODE_SILENT,
			]);
		}
		else {
			$this->error = 'invalid_database_drive['.$this->config['drive'].']';
			return false;
		}

		return true;
	}

	/**
	 * Transaction start
	 *
	 * @return bool true on success/false on failure
	*/
	public function transStart() : bool
	{
		return $this->db->pdo->beginTransaction();
	}

	/**
	 * Transaction commit
	 *
	 * @return bool true on success/false on failure
	*/
	public function transCommit() : bool
	{
		return $this->db->pdo->commit();
	}

	/**
	 * Transaction rollback
	 *
	 * @return bool true on success/false on failure
	*/
	public function transRollback() : bool
	{
		return $this->db->pdo->rollBack();
	}

	/**
	 * Get errors
	 *
	 * @return mixed
	*/
	public function errorInfo()
	{
		return $this->db === null ? null : $this->db->error();
	}
}
