<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Configuration
 *
 * @modified : 17 Aug 2022
 * @created  : 20 Sep 2019
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\ConfigInterface;

class Config implements ConfigInterface
{
	// @array
	protected $configs = [];

	/**
	 * Load configurations form file
	 *
	 * @param bool true on success/false on failure
	*/
	public function loadFromFile(string $configFilePath) : bool
	{
		if (is_file($configFilePath) === false || is_readable($configFilePath) === false)
			return false;

		$confs = configLoadArrayFile_($configFilePath);
		if (is_array($confs) === false)
			return false;

		return $this->loadFromArray($confs);
	}

	/**
	 * Load configurations form array (set multiple)
	 *
	 * @param array $params [key => value, key => value]
	 * @return bool true on success/false on failure
	*/
	public function loadFromArray(array $params) : bool
	{
		$rc = true;
		foreach ($params as $key => $value) {
			if (is_string($key) === false) {
				$rc = false;
				break;
			}
			$rc = $this->set($key, $value);
			if ($rc === false)
				break;
		}
		return $rc;
	}

	/**
	 * Has
	 *
	 * @param string
	 * @return bool true on success/false on failure
	*/
	public function has(string $key) : bool
	{
		return $this->has_($key, true);
	}

	/**
	 * Has multiple
	 *
	 * @param array
	 * @return bool true on success/false on failure
	*/
	public function hasMultiple(array $keys) : bool
	{
		foreach ($keys as $key) {
			$key = strtolower($key);
			$rc = $this->has_($key, false);
			if ($rc === false)
				return false;
		}
		return true;
	}

	/**
	 * Get
	 *
	 * @param string
	 * @return mixed on success/bool false on failure
	*/
	public function get(string $key)
	{
		return $this->get_($key, true);
	}

	/**
	 * Get multiple
	 *
	 * @param array
	 * @return array
	*/
	public function getMultiple(array $keys) : array
	{
		$result = [];

		foreach ($keys as $key) {
			$key = strtolower($key);
			if ($this->has_($key, false) === false)
				return [];
			$result[$key] = $this->get_($key, false);
		}
		return $result;
	}

	/**
	 * Get all
	 *
	 * @return array
	*/
	public function getAll() : array
	{
		return $this->configs;
	}

	/**
	 * Get all configuration by reference
	 *
	 * @return array
	*/
	public function &getAllRef() : array
	{
		return $this->configs;
	}

	/**
	 * Set
	 *
	 * @param string
	 * @param mixed
	 * @return bool
	*/
	public function set(string $key, $value) : bool
	{
		if ($key === '' || $key === ' ')
			return false;
		$key = strtolower($key);
		$this->configs[$key] = $value;
		return true;
	}

	/**
	 * Set multiple (alias of loadFromArray)
	 *
	 * @param array $params [key => value, key => value]
	 * @return bool true on success/false on failure
	*/
	public function setMultiple(array $params) : bool
	{
		return $this->loadFromArray($params);
	}

	/**
	 * Remove
	 *
	 * @param string
	 * @return bool true on success/false on failure
	*/
	public function remove(string $key) : bool
	{
		$key = strtolower($key);
		$rc = $this->has_($key, false);
		if ($rc === false)
			return false;
		unset($this->configs[$key]);
		return true;
	}

	/**
	 * Has
	 *
	 * @param string
	 * @param bool
	 * @return bool true on success/false on failure
	*/
	protected function has_(string $key, bool $tolower) : bool
	{
		if ($tolower === true)
			$key = strtolower($key);
		return array_key_exists($key, $this->configs) === true ? true : false;
	}

	/**
	 * Get
	 *
	 * @param string
	 * @param bool
	 * @return mixed on success/bool false on failure
	*/
	protected function get_(string $key, bool $tolower)
	{
		if ($tolower === true)
			$key = strtolower($key);
		return array_key_exists($key, $this->configs) === true ? $this->configs[$key] : false;
	}
}

/**
 * Require PHP array file
 *
 * @param string
 * @return array
*/
function &configLoadArrayFile_(string& $filename)
{
	$data = require($filename);
	if (is_array($data) === false) {
		$false = false;
		return $false;
	}
	return $data;
}
