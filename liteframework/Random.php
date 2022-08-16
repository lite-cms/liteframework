<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Random
 *
 * @modified : 14 Dec 2020
 * @created  : 17 Oct 2019
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

use LiteFramework\RandomInterface;

class Random implements RandomInterface
{
	/**
	 * Random Bytes
	 *
	 * @param int $length
	 * @param bool $toHex
	 * return string
	*/
	public function bytes(int $length = 32, bool $toHex = true) : string
	{
		if (intval($length) <= 8)
			$length = 32;

		if (function_exists('\random_bytes') === true)
			return $toHex === true ? \bin2hex(\random_bytes($length)) : \random_bytes($length);

		if (function_exists('\mcrypt_create_iv') === true) {
			return $toHex === true ? \bin2hex(mcrypt_create_iv($length, \MCRYPT_DEV_URANDOM)) : 
				\mcrypt_create_iv($length, \MCRYPT_DEV_URANDOM);
		}

		if (function_exists('\openssl_random_pseudo_bytes') === true) {
			return $toHex === true ? \bin2hex(\openssl_random_pseudo_bytes($length)) : 
				\openssl_random_pseudo_bytes($length);
		}

		return '';
	}

	/**
	 * Random Number
	 *
	 * @param int
	 * @param int
	 * return int
	*/
	public function int(int $min, int $max) : int
	{
		return mt_rand($min, $max);
	}
}
