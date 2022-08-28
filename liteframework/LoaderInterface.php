<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Loader Interface
 *
 * @modified : 28 Aug 2022
 * @created  : 14 Dec 2020
 * @author   : Ali Bakhtiar
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface LoaderInterface
{
	/**
	 * Set varailable
	 *
	 * @param string
	 * @param mixed
	 * @return void
	*/
	public static function set(string $key, $value);

	/**
	 * Set varailable - passing by reference
	 *
	 * @param string
	 * @param mixed
	 * @return void
	*/
	public static function setRef(string $key, &$value);

	/**
	 * Get varailable
	 *
	 * @param string
	 * @return bool false on failure/mixed on success
	*/
	public static function get(string $key);

	/**
	 * Get varailable - passing by reference
	 *
	 * @param string
	 * @return bool false on failure/mixed on success
	*/
	public static function &getRef(string $key);

	/**
	 * Remove varailable
	 *
	 * @param string
	 * @return bool false on failure/true on success
	*/
	public static function remove(string $key);
}
