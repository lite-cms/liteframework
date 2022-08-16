<?php
/**
 * LiteFramework
 * Copyright (c) 2022 Ali Bakhtiar <https://persianicon.com/@ali>
 * MIT License
 * https://litecms.ir
*/

/**
 * Hash interface
 *
 * @modified : 01 Feb 2022
 * @created  : 16 Oct 2019
 * @author   : Ali Bakhtiar
*/

declare(strict_types=1);

namespace LiteFramework;

defined('LITEF_PATH') OR exit('Restricted access');

interface HashInterface
{
	/**
	 * Set salt
	 *
	 * @param string
	 * @return void
	*/
	public function setSalt(string $salt);

	/**
	 * Get salt
	 *
	 * @return string
	*/
	public function getSalt() : string;

	/**
	 * MD5
	 *
	 * @param string
	 * @return string | length 32
	*/
	public function md5(string $input, string $salt = '') : string;

	/**
	 * SHA1 - 128
	 *
	 * @param string
	 * @return string | length 40
	*/
	public function sha1(string $input, string $salt = '') : string;

	/**
	 * SHA2 - 256
	 *
	 * @param string
	 * @return string | length 64
	*/
	public function sha2(string $input, string $salt = '') : string;

	/**
	 * SHA3 - 384
	 *
	 * @param string
	 * @return string | length 96
	*/
	public function sha3(string $input, string $salt = '') : string;

	/**
	 * SHA5 - 512
	 *
	 * @param string
	 * @return string | length 128
	*/
	public function sha5(string $input, string $salt = '') : string;
}
