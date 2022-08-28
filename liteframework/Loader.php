<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Loader
 *
 * @modified : 28 Aug 2022
 * @created  : 14 Dec 2020
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\LoaderInterface;

class Loader implements LoaderInterface
{
	// @array
	protected static $container = [];

	/**
	 * Set varailable
	 *
	 * @param string
	 * @param mixed
	 * @return void
	*/
	public static function set(string $key, $value)
	{
		self::$container[$key] = $value;
		return;
	}

	/**
	 * Set varailable - passing by reference
	 *
	 * @param string
	 * @param mixed
	 * @return void
	*/
	public static function setRef(string $key, &$value)
	{
		self::$container[$key] =& $value;
		return;
	}

	/**
	 * Get varailable
	 *
	 * @param string
	 * @return bool false on failure/mixed on success
	*/
	public static function get(string $key)
	{
		return array_key_exists($key, self::$container) === false ? false : self::$container[$key];
	}

	/**
	 * Get varailable - passing by reference
	 *
	 * @param string
	 * @return bool false on failure/mixed on success
	*/
	public static function &getRef(string $key)
	{
		if (array_key_exists($key, self::$container) === false) {
			$val = false;
			return $val;
		}
		return self::$container[$key];
	}

	/**
	 * Remove varailable
	 *
	 * @param string
	 * @return bool false on failure/true on success
	*/
	public static function remove(string $key)
	{
		if (array_key_exists($key, self::$container) === false)
			return false;

		unset(self::$container[$key]);
		return true;
	}
}
