<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Common functions
 *
 * @modified : 17 Aug 2022
 * @created  : 26 Nov 2018
*/

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

/**
 * Array order by key
 * easy way to sort database-style results.
 *
 * http://php.net/manual/en/function.array-multisort.php#100534
 * e.g: $sorted = arrayOrderBy($data, 'volume', SORT_DESC, 'edition', SORT_ASC);
 *
 * @return array
*/
if (function_exists('\LiteFramework\arrayOrderBy') === false)
{
	function arrayOrderBy($args = []) : array
	{
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = [];
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}
}

/**
 * This function returns the maximum files size that can be uploaded in PHP
 *
 * @returns int File size in bytes
*/
if (function_exists('\LiteFramework\getMaxFileUploadSize') === false)
{
	function getMaxFileUploadSize() : int
	{
		$s1 = convertPhpSizeToBytes(ini_get('post_max_size'));
		$s2 = convertPhpSizeToBytes(ini_get('upload_max_filesize'));
		return min($s1, $s2);
	}
}

/**
 * This function transforms the php.ini notation for numbers
 * (like '2M') to an integer (2*1024*1024 in this case)
 *
 * @source: https://stackoverflow.com/a/22500394/733523
 *
 * @param string $sSize
 * @return integer The value in bytes
*/
if (function_exists('\LiteFramework\convertPhpSizeToBytes') === false)
{
	function convertPhpSizeToBytes(string $sSize) : int
	{
		$sSuffix = strtoupper(substr($sSize, -1));
		if (in_array($sSuffix,array('P', 'T', 'G', 'M', 'K')) === false)
			return (int) $sSize;

		$iValue = substr($sSize, 0, -1);
		switch ($sSuffix) {
			case 'P':
				$iValue *= 1024;
				// Fallthrough intended
			case 'T':
				$iValue *= 1024;
				// Fallthrough intended
			case 'G':
				$iValue *= 1024;
				// Fallthrough intended
			case 'M':
				$iValue *= 1024;
				// Fallthrough intended
			case 'K':
				$iValue *= 1024;
			break;
		}

		return (int) $iValue;
	}
}

/**
 * Get value
 *
 * @param array $array
 * @param string $key
 * @param mixed $defaultValue
 * @return mixed
*/
if (function_exists('\LiteFramework\getValue') === false)
{
	function getValue(array& $array, string $key, $defaultValue = false)
	{
		return array_key_exists($key, $array) === true ? $array[$key] : $defaultValue;
	}
}
