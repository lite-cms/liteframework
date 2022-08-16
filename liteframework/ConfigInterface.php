<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Configuration Interface
 *
 * @modified : 17 Aug 2022
 * @created  : 20 Sep 2019
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface ConfigInterface
{
	/**
	 * Load configurations form file
	 *
	 * @param bool true on success/false on failure
	*/
	public function loadFromFile(string $configFilePath) : bool;

	/**
	 * Load configurations form array (set multiple)
	 *
	 * @param array $params [key => value, key => value]
	 * @return bool true on success/false on failure
	*/
	public function loadFromArray(array $params) : bool;

	/**
	 * Has
	 *
	 * @param string
	 * @return bool true on success/false on failure
	*/
	public function has(string $key) : bool;

	/**
	 * Has multiple
	 *
	 * @param array
	 * @return bool true on success/false on failure
	*/
	public function hasMultiple(array $keys) : bool;

	/**
	 * Get
	 *
	 * @param string
	 * @return mixed on success/bool false on failure
	*/
	public function get(string $key);

	/**
	 * Get multiple
	 *
	 * @param array
	 * @return array
	*/
	public function getMultiple(array $keys) : array;

	/**
	 * Get all configurations
	 *
	 * @return array
	*/
	public function getAll() : array;

	/**
	 * Get all configuration by reference
	 *
	 * @return array
	*/
	public function &getAllRef() : array;

	/**
	 * Set
	 *
	 * @param string
	 * @param mixed
	 * @return mixed on success/bool false on failure
	*/
	public function set(string $key, $value) : bool;

	/**
	 * Set multiple (alias of loadFromArray)
	 *
	 * @param array $params [key => value, key => value]
	 * @return bool true on success/false on failure
	*/
	public function setMultiple(array $params) : bool;

	/**
	 * Remove
	 *
	 * @param string
	 * @return bool true on success/false on failure
	*/
	public function remove(string $key) : bool;
}
